<?php
/**
 * 简述
 *
 * 详细说明(可略)
 *
 * @copyright Copyright&copy; 2016, Meizu.com
 * @author   zouzehua <zouzehua@meizu.com>
 * @version $Id: Lottery.php, v ${VERSION} 2016-7-7 15:49 Exp $
 */
namespace Yboard;

class LotteryService extends CommonService {
    /**
     * 谢谢参与的奖品id
     * @var int
     */
    private $not_win_pid = 4;
    /**
     * 再来一次的奖品id
     * @var int
     */
    private $again_win_pid = 5;

    public function __construct() {
        $not_win_pid         = getSetting('not_win_pid');
        $this->not_win_pid   = $not_win_pid ? $not_win_pid : 0;
        $again_win_pid       = getSetting('again_win_pid');
        $this->again_win_pid = $again_win_pid ? $again_win_pid : 0;
    }

    /**
     * 抽奖操作
     * @param $uid
     * @param $date
     */
    public function lottery($uid, $date) {
        $chance_model = $this->loadModel('Chance');
        $prize_model  = $this->loadModel('Prize');
        //这里用redis的dec(递减操作，防止大并发多抽奖问题出现)
        //原先是先去查找数据库，这里会有数据库网络堵塞，IO堵塞问题
        $dec_lottery_num = $chance_model->updateUserChanceCache($uid, $date, -1);
//        $lottery_num     = $this->getUserChance($uid, $date);
        //因为这里是递减过的，所以判断条件为<0
        if ($dec_lottery_num < 0) {
            $chance_model->updateUserChanceCache($uid, $date, 1);

            return $this->returnInfo(0, '你已经没有抽奖机会了');
        }

        $prize_lottery_array = [];
        $prize_array         = $this->getPrize();
        if ($prize_array) {
            foreach ($prize_array as $prize) {
                $prize_lottery_array[$prize['p_id']] = $prize['p_probability'];
            }
        }

        $win_pid = $this->getRand($prize_lottery_array);

        //不是谢谢参与和再来一次的话，则进行奖品库存减一
        if (!in_array($win_pid, [$this->again_win_pid, $this->not_win_pid])) {
            if (!isset($prize_array[$win_pid]) || (empty($prize_array[$win_pid]))) {
                $win_pid = $this->not_win_pid;
            } else {
                $p_inventory = $prize_model->increaseInventoryCache($win_pid, -1);
                if ($p_inventory < 0) {
                    $chance_model->updateUserChanceCache($uid, $date, 1);
                    $prize_model->increaseInventoryCache($win_pid, 1);

                    return $this->returnInfo(0, '奖品没有库存了哦');
                }
            }

        }

        //如果不是谢谢参与，则进行中奖信息的写入
        if ($win_pid != $this->not_win_pid) {
            $ret = $this->winning($win_pid, $uid, $date);
            if (!$ret['state']) {
                $chance_model->updateUserChanceCache($uid, $date, 1);
                $prize_model->increaseInventoryCache($win_pid, 1);

                return $this->returnInfo(0, $ret['message']);
            }
        }

        //如果不是再来一次，则进行机会的删除
        if ($win_pid != $this->again_win_pid) {
            $ret = $this->decChance($uid, $date, 1);
            if (!$ret['state']) {
                $chance_model->updateUserChanceCache($uid, $date, 1);
                $prize_model->increaseInventoryCache($win_pid, 1);

                return $this->returnInfo(0, $ret['message']);
            }
        }

        if (!in_array($win_pid, [$this->again_win_pid, $this->not_win_pid])) {
            if (!$prize_model->increaseInventory($win_pid, -1)) {
                $chance_model->updateUserChanceCache($uid, $date, 1);
                $prize_model->increaseInventoryCache($win_pid, 1);

                return $this->returnInfo(0, '奖品库存错误');
            }
        }

        return $this->returnInfo(1, '抽奖成功', ['p_id' => $win_pid, 'lottery_num' => $dec_lottery_num]);
    }


    /**
     * 今天抽奖机会
     * @param $uid
     * @param $date
     * @return int
     */
    public function getUserChance($uid, $date) {
        $chance_model = $this->loadModel('Chance');
        $chance_info  = $chance_model->getUserChance($uid, $date);

        return ($chance_info && isset($chance_info['c_num'])) ? intval($chance_info['c_num']) : 0;
    }

    /**
     * 获取奖品数据
     * @return mixed
     */
    public function getPrize() {
        $prize_model = $this->loadModel('Prize');

        return $prize_model->getAllPrize();
    }

    /**
     * (离散型抽奖)抽奖算法
     *
     * @param $proArr = ['pid'=>'p_probability']
     * @return int|string
     */
    function getRand($prizes) {
        $index          = $this->not_win_pid;
        $probabilitySum = array_sum($prizes);
        $rand           = mt_rand(1, $probabilitySum);
        foreach ($prizes as $key => $probability) {
            if ($rand <= $probability) {
                $index = $key;
                break;
            } else {
                $rand -= $probability;
            }
        }

        return $index;
    }

    /**
     * 中奖信息写入数据库
     * @param $p_id
     * @param $uid
     * @param $date
     * @return array
     */
    public function winning($p_id, $uid, $date) {
        $winning_model = $this->loadModel('Winning');
        $winning_info  = $winning_model->getByUidAndDate($uid, $date);
        if ($winning_info) {
            $update_info = [
                'update_time' => NOW_TIME,
                'w_num[+]'    => 1,
                'update_date' => $date,
            ];
            if (false === $winning_model->updateById($winning_info['w_id'], $update_info)) {
                return $this->returnInfo(0, '中奖数据更新失败');
            }
        } else {
            $winning_info = [
                'w_uid'       => $uid,
                'update_time' => NOW_TIME,
                'w_num'       => 1,
                'update_date' => $date,
                'create_time' => NOW_TIME
            ];
            $w_id         = $winning_model->save($winning_info);
            if (!$w_id) {
                return $this->returnInfo(0, '中奖数据插入失败');
            }
            $winning_info['w_id'] = $w_id;
        }

        if ($winning_info && $winning_info['w_id']) {
            $win_info_model = $this->loadModel('WinningInfo');
            if (false === $win_info_model->save([
                    'w_id'        => $winning_info['w_id'],
                    'w_uid'       => $uid,
                    'p_id'        => $p_id,
                    'create_time' => NOW_TIME,
                    'create_date' => $date
                ])
            ) {
                return $this->returnInfo(0, '插入失败');
            }
        }

        return $this->returnInfo(1, '中奖成功');
    }

    /**
     * 减少机会
     * @param $uid
     * @param $date
     * @param $num
     * @return bool
     */
    public function decChance($uid, $date, $num) {
        if ($num == 0) {
            return $this->returnInfo(0, '抽奖机会不能为0');
        }
        $num          = abs($num);
        $chance_model = $this->loadModel('Chance');
        $chance_info  = $chance_model->getUserInfo($uid);
        if (!$chance_info) {
            return $this->returnInfo(0, '你今天没有抽奖机会');
        } else {
            if ($chance_info['update_date'] == $date) {
                $update_data = [
                    'c_num[-]'    => $num,
                    'update_time' => NOW_TIME
                ];
            } else {
                $update_data = [
                    'c_num'       => $num,
                    'update_date' => $date,
                    'update_time' => NOW_TIME
                ];
            }
            $ret = $chance_model->updateById($chance_info['c_id'], $update_data);
            if (!$ret) {
                return $this->returnInfo(0, '抽奖机会减少失败');
            }
        }
        if ($ret) {
            $chance_info_model = $this->loadModel('ChanceInfo');
            if (!$chance_info_model->save([
                'ci_uid'      => $uid,
                'c_id'        => $chance_info['c_id'],
                'ci_type'     => 2,
                'ci_category' => '',
                'ci_num'      => $num,
                'create_time' => NOW_TIME
            ])
            ) {
                return $this->returnInfo(0, '抽奖机会日志添加失败');
            }
        }
        unset($chance_info);

        return $this->returnInfo(1, '抽奖机会更新成功');

    }

    /**
     * 今天是否添加过抽奖机会
     * @param $uid
     * @param $date
     * @return bool
     */
    public function isTodayAddChance($uid, $date) {
        $key         = str_replace(['{uid}', '{today}'], [$uid, $date], USER_ISCHANCE_KEY);
        $redis_class = memory();
        if ($redis_class->increment($key, 1) == 1) {
            $chance_model = $this->loadModel('Chance');
            $chance_info  = $chance_model->getUserChance($uid, $date);

            return $chance_info ? true : false;
        }

        return true;

    }

    /**
     * 给用户增加机会
     * @param $uid
     * @param $date
     * @param $num
     * @return mixed
     */
    public function incChance($uid, $date, $num) {
        if ($num == 0) {
            return $this->returnInfo(0, '抽奖机会不能为0');
        }
        $num          = abs($num);
        $chance_model = $this->loadModel('Chance');
        $chance_info  = $chance_model->getUserInfo($uid);
        if (!$chance_info) {
            $chance_info = [
                'c_uid'       => $uid,
                'c_num'       => $num,
                'create_time' => NOW_TIME,
                'update_time' => NOW_TIME,
                'update_date' => $date
            ];
            $ret         = $chance_model->save($chance_info);
            if (!$ret) {
                return $this->returnInfo(0, '机会增加失败');
            }
            $chance_info['c_id'] = $ret;
        } else {
            if ($chance_info['update_date'] == $date) {
                $update_data = [
                    'c_num[+]'    => $num,
                    'update_time' => NOW_TIME
                ];
            } else {
                $update_data = [
                    'c_num'       => $num,
                    'update_date' => $date,
                    'update_time' => NOW_TIME
                ];
            }
            $ret = $chance_model->updateById($chance_info['c_id'], $update_data);
            if (!$ret) {
                return $this->returnInfo(0, '机会增加失败');
            }
        }
        if ($ret) {
            $chance_info_model = $this->loadModel('ChanceInfo');
            if (!$chance_info_model->save([
                'ci_uid'      => $uid,
                'c_id'        => $chance_info['c_id'],
                'ci_type'     => 1,
                'ci_category' => '',
                'ci_num'      => $num,
                'create_time' => NOW_TIME
            ])
            ) {
                if (!$ret) {
                    return $this->returnInfo(0, '机会增加日志添加失败');
                }
            }
        }
        unset($chance_info);

        return $this->returnInfo(1, '机会增加成功');
    }

    public function initChance($uid, $date) {
        $chance_model        = $this->loadModel('Chance');
        $is_today_add_chance = $this->isTodayAddChance($uid, $date);
        $init_chance         = getSetting('init_chance');
        if (!$is_today_add_chance && $init_chance) {
            $this->incChance($uid, $date, $init_chance);
            $chance_model->updateUserChanceCache($uid, $date, (int)$init_chance);
        }

        return true;
    }
}