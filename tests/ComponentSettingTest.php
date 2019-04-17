<?php
/**
 * Created by PhpStorm.
 * User: zjw
 * Date: 2017/8/7
 * Time: 下午6:00
 */

namespace pheme\settings\tests;

use pheme\settings\components\Settings;
use pheme\settings\models\Setting;
use Yii;

class ComponentSettingTest extends TestCase
{
    /**
     * @var \pheme\settings\components\Settings
     */
    public $setting;

    public function setUp()
    {
        parent::setUp();
        $this->setting = Yii::$app->settings;
        $this->setting->init();
        $this->model->value = 'i am testSet value';
        $this->model->section = 'testSetKey';
        $this->model->type = 'string';
        $this->model->modified = time();
        $this->model->active = '0';
        $this->model->key = 'testSetKey';
        $this->model->save();
    }

    public function testSet()
    {
        $res = $this->setting->set('testSetKey', 'i am testSet value', 'testSetKey');
        $this->assertTrue($res, '通过组件来修改testSetKey的section');
    }

    public function testGet()
    {
        $this->setting->activate('testSetKey', 'testSetKey');
        $res = $this->setting->get('testSetKey', 'testSetKey');
        $this->assertTrue($res == 'i am testSet value');
        $res1 = $this->setting->get('testSetKey.testSetKey');
        $this->assertTrue($res1 == 'i am testSet value');
    }

    public function testHas()
    {
        $this->setting->activate('testSetKey', 'testSetKey');
        $res = $this->setting->has('testSetKey', 'testSetKey');
        $this->assertTrue($res);
    }

    public function testDelete()
    {
        $res = $this->setting->delete('testSetKey', 'testSetKey');
        $this->assertTrue($res == 1);
    }

    public function testDeleteAll()
    {
        $res = $this->setting->deleteAll();
        $this->assertTrue($res > 0);
    }

    public function testActivate()
    {
        $res = $this->setting->activate('testSetKey', 'testSetKey');
        $this->assertTrue($res);
    }

    public function testDeActivate()
    {
        $this->setting->activate('testSetKey', 'testSetKey');
        $res = $this->setting->deactivate('testSetKey', 'testSetKey');
        $this->assertTrue($res);
    }

    public function testGetRawConfig()
    {
        $this->setting->activate('testSetKey', 'testSetKey');
        $this->setting->get('testSetKey', 'testSetKey');
        $res = $this->setting->getRawConfig();
        $this->assertTrue($res['testSetKey']['testSetKey'][0] == $this->model->value);
    }

    public function testClearCache()
    {
        $res = $this->setting->clearCache();
        $this->assertTrue($res);
    }

    public function testGetModelClass()
    {
        $this->assertTrue($this->setting->modelClass == (new Settings())->modelClass);
    }

    public function testAutodetectEmailType()
    {
        $this->setting->set('app.email', 'test@example.com');
        $model = Setting::find()->where(['section' => 'app'])->andWhere(['key' => 'email'])->one();
        $this->assertEquals('email', $model->type);
    }

    public function testAutodetectUrlType()
    {
        $this->setting->set('app.url', 'https://example.com');
        $model = Setting::find()->where(['section' => 'app'])->andWhere(['key' => 'url'])->one();
        $this->assertEquals('url', $model->type);
    }

    public function testAutodetectIPType()
    {
        $this->setting->set('app.ip', '8.8.8.8');
        $model = Setting::find()->where(['section' => 'app'])->andWhere(['key' => 'ip'])->one();
        $this->assertEquals('ip', $model->type);
    }

    public function testAutodetectBooleanType()
    {
        $this->setting->set('app.bool', 'true');
        $model = Setting::find()->where(['section' => 'app'])->andWhere(['key' => 'bool'])->one();
        $this->assertEquals('boolean', $model->type);
    }

    public function testAutodetectIntegerType()
    {
        $this->setting->set('app.int', '42');
        $model = Setting::find()->where(['section' => 'app'])->andWhere(['key' => 'int'])->one();
        $this->assertEquals('integer', $model->type);
    }

    public function testAutodetectFloatType()
    {
        $this->setting->set('app.float', '42.42');
        $model = Setting::find()->where(['section' => 'app'])->andWhere(['key' => 'float'])->one();
        $this->assertEquals('float', $model->type);
    }

    public function testAutodetectJsonType()
    {
        $this->setting->set('app.json', '{"test":42}');
        $model = Setting::find()->where(['section' => 'app'])->andWhere(['key' => 'json'])->one();
        $this->assertEquals('object', $model->type);
    }

}
