<?php
/**
 * @author dogstar <chanzonghuang@gmail.com> 2015-02-22
 */

class ModelProxy_UserBaseInfo extends PhalApi_ModelProxy {

	protected function doGetData($query) {
		$model = new Model_User();

		return $model->getByUserId($query->id);
	}

	protected function getKey($query) {
		return 'userbaseinfo_' . $query->id;
	}

	protected function getExpire($query) {
		return 600;
	}
}
