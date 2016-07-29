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
    private $not_win_pid = 1;

    /**
     * 再来一次的奖品id
     * @var int
     */
    private $again_win_pid = 2;

    /**
     * 每个用户抽奖次数每天初始值
     * @var int
     */
    private $init_chance = 3;

    /**
     * 抽奖操作
     */
    public function lottery($uid) {
        $prize_array = $this->getLotteryPrize();
    }

    private function getLotteryPrize() {
        $prize_lottery_array = [];
        $prize_array         = $this->getPrize();
        if ($prize_array) {
            foreach ($prize_array as $prize) {
                $prize_lottery_array[$prize['p_id']] = $prize['p_probability'];
            }
        }

        return $prize_lottery_array;
    }

    public function getPrize() {
        $prize_model = $this->loadModel('Prize');

        return $prize_model->getAllPrize();
    }

    /**
     * 抽奖算法
     *
     * @param $proArr = ['pid'=>'p_probability']
     * @return int|string
     */
    public function getRand($proArr) {
        $p_id = $this->not_win_pid;
        //根据奖品权重进行一个由高到底排序
        $proArr = asort($proArr);

        $p_id_array  = array_keys($proArr);
        $prize_array = array_values($proArr);

        $new_prize_array = [];
        //画出一个扇形的，每个奖品落到扇区的对应位置上面
        $proSum = 0;
        foreach ($prize_array as $key => $proCur) {
            $proSum += $proCur;
            $new_prize_array[$key] = $proSum;
        }

        $randNum = mt_rand(1, $proSum);
        //概率数组循环
        foreach ($new_prize_array as $key => $proCur) {
            if ($key <= 0) {
                if ($randNum <= $proCur) {
                    $p_id = $p_id_array[$key];
                    break;
                }
            } elseif ($new_prize_array[$key - 1] > $randNum && $randNum <= $proCur) {
                $p_id = $p_id_array[$key];
                break;
            }
        }
        unset ($proArr);

        return $p_id;
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
     * 今天是否添加过抽奖机会
     * @param $uid
     * @param $date
     * @return bool
     */
    public function isTodayAddChance($uid, $date) {
        $chance_model = $this->loadModel('Chance');
        $chance_info  = $chance_model->getUserChance($uid, $date);

        return $chance_info ? true : false;
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
            $chance_info         = [
                'c_uid'       => $uid,
                'c_num'       => $num,
                'create_time' => NOW_TIME,
                'update_time' => NOW_TIME,
                'update_date' => $date
            ];
            $ret                 = $chance_model->save($chance_info);
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
            $ret = $chance_model->update($chance_info['c_id'], $update_data);
        }
        if ($ret) {
            $chance_info_model = $this->loadModel('ChanceInfo');
            $chance_info_model->insert([
                'ci_uid'      => $uid,
                'c_id'        => $chance_info['c_id'],
                'ci_type'     => 1,
                'ci_category' => '',
                'ci_num'      => $num,
                'create_time' => NOW_TIME
            ]);
        }
        unset($chance_info);

        return $ret;
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
            $ret = $chance_model->update($chance_info['c_id'], $update_data);
        }
        if ($ret) {
            $chance_info_model = $this->loadModel('ChanceInfo');
            $chance_info_model->insert([
                'ci_uid'      => $uid,
                'c_id'        => $chance_info['c_id'],
                'ci_type'     => 2,
                'ci_category' => '',
                'ci_num'      => $num,
                'create_time' => NOW_TIME
            ]);
        }
        unset($chance_info);

        return $ret;

    }
}