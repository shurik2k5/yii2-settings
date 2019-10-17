<?php

namespace shurik2k5\settings\tests;

use Yii;
use pheme\settings\models\Setting;
use PHPUnit_Framework_TestCase;
use yii\helpers\ArrayHelper;

class TestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @var \pheme\settings\models\Setting
     */
    public $model;

    protected function setUp()
    {
        parent::setUp();
        $this->mockApplication();
        $this->createTestDbData();
        $this->model = new Setting();
    }

    /**
     * Populates Yii::$app with a new application
     * The application will be destroyed on tearDown() automatically.
     *
     * @param array $config The application configuration, if needed
     * @param string $appClass name of the application class to create
     * @return \yii\console\Application
     */
    protected function mockApplication($config = [], $appClass = '\yii\console\Application')
    {
        return new $appClass(ArrayHelper::merge([
            'id' => 'testapp',
            'basePath' => __DIR__,
            'vendorPath' => $this->getVendorPath(),
            'components' => [
                'db' => [
                    'class' => 'yii\db\Connection',
                    'dsn' => 'sqlite::memory:',
                ],
                'i18n' => [
                    'translations' => [
                        '*' => [
                            'class' => 'yii\i18n\PhpMessageSource',
                        ]
                    ]
                ],
                'settings' => [
                    'class' => 'pheme\settings\components\Settings',
                    'autoDecodeJson' => true,
                    'frontCache' => 'frontCache2'
                ],
                'cache' => [
                    'class' => 'yii\caching\ArrayCache'
                ],
                'frontCache' => [
                    'class' => 'yii\caching\ArrayCache'
                ],

            ],
            'modules' => [
                'settings' => [
                    'class' => 'pheme\settings\Module',
                    'sourceLanguage' => 'en'
                ]
            ]
        ], $config));
    }

    /**
     * @return string vendor path
     */
    protected function getVendorPath()
    {
        return dirname(__DIR__) . '/vendor';
    }

    protected function createTestDbData()
    {
        //$this->mockApplication()->runAction('migrate/up', ['interactive' => false, 'migrationPath' => __DIR__.'/../migrations']);

        $db = Yii::$app->getDb();
        $this->applyMigrations($db);
    }

    protected function applyMigrations($db)
    {
        require_once __DIR__ . '/../src/migrations/m140618_045255_create_settings.php';
        require_once __DIR__ . '/../src/migrations/m151126_091910_add_unique_index.php';

        (new \m140618_045255_create_settings(['db' => $db, 'compact' => true]))->up();
        (new \m151126_091910_add_unique_index(['db' => $db, 'compact' => true]))->up();
    }

    protected function tearDown()
    {
        $this->destroyTestDbData();
        $this->destroyApplication();
    }

    protected function destroyTestDbData()
    {
        $db = Yii::$app->getDb();
        $db->createCommand()->dropTable('{{%settings}}')->execute();
    }

    /**
     * Destroys application in Yii::$app by setting it to null.
     */
    protected function destroyApplication()
    {
        if (Yii::$app && Yii::$app->has('session', true)) {
            Yii::$app->session->close();
        }
        Yii::$app = null;
    }
}
