<?php
/**
 * Created by PhpStorm.
 * User: zjw
 * Date: 2017/8/7
 * Time: 下午3:56
 */

namespace shurik2k5\settings\tests;

use pheme\settings\models\Setting;

class SettingModelTest extends TestCase
{
    /**
     * @var \pheme\settings\models\Setting
     */
    public $model;

    public function setUp()
    {
        parent::setUp();
    }

    /**
     * type 必须在指定范围内  通过getTypes
     */
    public function testRulesType()
    {
        $this->model->value = "1";
        $this->model->section = "i am section";
        $this->model->type = 'integer';
        $this->model->modified = "i am created";
        $this->model->active = "1";
        $allowType = implode(',', $this->model->getTypes("true"));
        $this->assertTrue($this->model->validate(), "type must be in " . $allowType);
        $this->model->type = 'doubles';
        $this->assertFalse($this->model->validate(), "type must be in " . $allowType);
    }

    /**
     * active must be boolean
     */
    public function testRulesActive()
    {
        $this->model->value = "1";
        $this->model->section = "i am section";
        $this->model->type = 'integer';
        $this->model->modified = "i am created";
        $this->model->active = 2;
        $this->assertFalse($this->model->validate(), "active must be bool");
    }

    public function testAdd()
    {
        $this->model->key = "testAdd";
        $this->model->value = "i am value";
        $this->model->section = "testAdd";
        $this->model->type = 'integer';
        $this->model->modified = "i am created";
        $this->model->active = "1";
        $this->assertFalse($this->model->save(), 'value must be integer');

        $this->model->active = 0;
        $this->model->type = "string";
        $this->assertTrue($this->model->save());

        $this->model->type = 'object';
        $this->model->value = '{"test":42}}';
        $this->assertFalse($this->model->save(), 'save invalid json value');

        $this->model->value = '{"test":42}';
        $this->assertTrue($this->model->save(), 'save json value');

        $this->assertTrue(1 == $this->model->delete());

    }

    public function testUpdate()
    {
        $this->model->value = "i am value";
        $this->model->section = "testUpdate";
        $this->model->type = 'string';
        $this->model->key = "testUpdate";
        $this->model->active = "1";
        $this->model->save();
        $model = Setting::findOne(['id' => $this->model->id]);
        $model->section = "testUpdated";
        $this->assertTrue($model->save());
    }

    public function testDelete()
    {
        $this->model->value = "i am value";
        $this->model->section = "testUpdate";
        $this->model->type = 'string';
        $this->model->key = "testUpdate";
        $this->model->active = "1";
        $this->model->save();
        $model = Setting::findOne(['active' => '1']);
        $this->assertTrue(1 == $model->delete());
    }

    public function testIncorrectTypeSave()
    {
        $this->model->value = "i am value";
        $this->model->section = "testUpdate";
        $this->model->type = 'wrong';
        $this->model->key = "testUpdate";
        $this->model->active = "1";
        $res = $this->model->save();
        $this->assertFalse($res);
        $this->assertArrayHasKey('type', $this->model->errors);
    }
}
