<?php
/**
 * 简述
 *
 * 详细说明(可略)
 *
 * @copyright Copyright&copy; 2016, Meizu.com
 * @author   zouzehua <zouzehua@meizu.com>
 * @version $Id: SettingService.class.php, v ${VERSION} 2016-8-1 11:26 Exp $
 */

namespace Yboard;


class SettingService extends CommonService {
    private $setting_model;

    public function __construct() {
        $this->setting_model = $this->loadModel('Setting');
    }


    public function insertOrUpdate($post) {

        $keys = array_keys($post);

        $existed     = $this->getBykeys($keys);
        $insert_data = array_diff_key($post, $existed);
        foreach ($insert_data as $skey => $svalue) {
            $svalue = $this->setPackValue($skey, $svalue);
            $this->setting_model->save(['skey' => $skey, 'svalue' => $svalue]);
        }
        $update_data = array_uintersect_uassoc($post, $existed, "strcasecmp", "strcasecmp");
        foreach ($update_data as $skey => $svalue) {
            $svalue = $this->setPackValue($skey, $svalue);

            $this->setting_model->update($skey, ['svalue' => $svalue]);
        }

        return $this->returnInfo(1, '设置成功');
    }

    public function getBykeys($keys = []) {
        $setting = $result_data = [];
        if ($keys && !is_array($keys)) {
            $setting                       = $this->setting_model->getById($keys);
            $result_data[$setting['skey']] = $this->unSetPackValue($setting['skey'], $setting['svalue']);

            return $result_data;
        }
        if (empty($keys)) {
            $setting = $this->setting_model->getAll();
        }
        if ($keys && is_array($keys)) {
            $setting = $this->setting_model->getByIds($keys);
        }
        if ($setting) {
            foreach ($setting as $key => $value) {
                $result_data[$value['skey']] = $this->unSetPackValue($value['skey'], $value['svalue']);
            }
        }

        return $result_data;


    }

    private function unSetPackValue($key, $value) {
        return in_array($key, $this->packKey()) ? unserialize($value) : $value;
    }

    private function packKey() {
        return [];
    }

    private function setPackValue($key, $value) {
        return in_array($key, $this->packKey()) ? serialize($value) : $value;
    }
}