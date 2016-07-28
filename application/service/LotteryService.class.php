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

    public function getPrize() {
        $prize_model = $this->loadModel('Prize');

        return $prize_model->getAllPrize();
    }
}