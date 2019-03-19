<?php
/**
 * Link         :   http://www.phpcorner.net
 * User         :   qingbing<780042175@qq.com>
 * Date         :   2019-01-23
 * Version      :   1.0
 */

namespace Controllers;


use Render\Abstracts\Controller;
use TestApp\Models\TestForm;

class TestController extends Controller
{
    public function actionTest()
    {
        $model = new TestForm();
        // 选项信息
        var_dump($model->getOptions());

        // 属性标签
        $attributeNames = $model->attributeNames();
        var_dump($attributeNames);

        // 属性名称
        $attributeNames = $model->attributeLabels();
//        $attributeNames = $model->getAttributeLabels(); // 和 $model->attributeLabels() 相同
        var_dump($attributeNames);

        // 属性值
        $attributes = $model->getAttributes();
        var_dump($attributes);


        // 设置属性值
        $model->setAttributes([
            'sex' => '10001',
        ]);
        $attributes = $model->getAttributes();
        var_dump($attributes);


//        var_dump($model);

    }

    public function actionWidget()
    {
        $model = new TestForm();
        $this->render('widget', [
            'model' => $model,
        ]);
    }
}