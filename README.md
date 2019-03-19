# pf-widgets-form-generator
## 描述
渲染部件——form 表单构造器，分为表单选项和表单渲染

## 注意事项
- 该小部件基于"qingbing/php-html"，"qingbing/php-render"开发
- 如果需要用到文件上传表单，需要引入"qingbing/php-upload"
- 如果需要使用编辑器，需要引入"qingbing/php-kindeditor"("qingbing/php-assets-manager"会被同步引入)
- 该部件定义的常量全部参考"\FormGenerator"
- "\Abstracts\FormOption" 为表单定义基类
- "\Widgets\FormGenerator" 为表单渲染类，如果要使用验证，需要手动拷贝并加载js包，参考"test/controllers/views/test/widget.php"

## 使用方法
### 1.1 表单选项定义示例
```php
class TestForm extends FormOption
{
    /**
     * 定义的表单项目
     * @return mixed
     */
    public function getOptions()
    {
        return [
            [
                'code' => 'password',
                'label' => '设置密码',
                'input_type' => \FormGenerator::INPUT_TYPE_PASSWORD,
//                'data_type' => \FormGenerator::DATA_TYPE_PASSWORD, // default
                'allow_empty' => false,
            ],
            [
                'code' => 'comparePassword',
                'label' => '确认密码',
                'input_type' => \FormGenerator::INPUT_TYPE_PASSWORD,
                'data_type' => \FormGenerator::DATA_TYPE_COMPARE,
                'compare_field' => 'password',
                'allow_empty' => false,
            ],
            [
                'code' => 'sex',
                'label' => '性别',
                'input_type' => \FormGenerator::INPUT_TYPE_SELECT,
//                'data_type' => \FormGenerator::DATA_TYPE_SELECT, // default
                'input_data' => [
                    '10000' => '密',
                    '10001' => '男',
                    '10002' => '女',
                ],
                'allow_empty' => false,
            ],
            [
                'code' => 'hobby',
                'label' => '爱好',
                'input_type' => \FormGenerator::INPUT_TYPE_SELECT,
                'data_type' => \FormGenerator::DATA_TYPE_CHOICE,
                'max' => 3,
                'min' => 2,
                'input_data' => [
                    '10001' => '跑步',
                    '10002' => '登山',
                    '10003' => '游泳',
                    '10004' => '写字',
                    '10005' => '唱歌',
                ],
                'allow_empty' => false,
            ],
            [
                'code' => 'checked',
                'label' => '是否同意',
                'input_type' => \FormGenerator::INPUT_TYPE_CHECKBOX, // 输入类型无需数据类型，最特殊的一个，其实隐藏为range
                'allow_empty' => false,
            ],
            [
                'code' => 'fruit',
                'label' => '水果',
                'input_type' => \FormGenerator::INPUT_TYPE_CHECKBOX_LIST,
//                'data_type' => \FormGenerator::DATA_TYPE_CHOICE, // 只有一种数据类型
                'min' => 2,
                'max' => 3,
                'input_data' => [
                    '10001' => '苹果',
                    '10002' => '桌子',
                    '10003' => '梨子',
                    '10004' => '香蕉',
                ],
                'allow_empty' => false,
            ],
            [
                'code' => 'avatar',
                'label' => '头像',
                'input_type' => \FormGenerator::INPUT_TYPE_FILE,
//                'data_type' => \FormGenerator::DATA_TYPE_FILE, // 只有一种数据类型
                'file_extensions' => 'jpg|jpeg|png',
                'allow_empty' => false,
            ],
            [
                'code' => 'lover',
                'label' => '最爱的水果',
                'input_type' => \FormGenerator::INPUT_TYPE_RADIO_LIST,
//                'data_type' => \FormGenerator::DATA_TYPE_CHECKED, // 只有一种数据类型
                'input_data' => [
                    '10001' => '苹果',
                    '10002' => '梨子',
                    '10003' => '香蕉',
                ],
                'allow_empty' => false,
            ],
            [
                'code' => 'content',
                'label' => '内容',
                'input_type' => \FormGenerator::INPUT_TYPE_EDITOR,
                'allow_empty' => false,
            ],
            [
                'code' => 'hidden',
                'label' => '隐藏域',
                'input_type' => \FormGenerator::INPUT_TYPE_HIDDEN,
                'data_type' => \FormGenerator::DATA_TYPE_STRING,
                'default' => "hide",
                'allow_empty' => false,
            ],
            [
                'code' => 'description',
                'label' => '描述',
                'input_type' => \FormGenerator::INPUT_TYPE_TEXTAREA,
                'data_type' => \FormGenerator::DATA_TYPE_STRING,
                'default' => "This is description",
                'allow_empty' => true,
            ],
            [
                'code' => 'required_field',
                'label' => '必填',
                'input_type' => \FormGenerator::INPUT_TYPE_TEXT,
                'data_type' => \FormGenerator::DATA_TYPE_REQUIRED,
            ],
            [
                'code' => 'email_field',
                'label' => '邮件',
                'input_type' => \FormGenerator::INPUT_TYPE_TEXT,
                'data_type' => \FormGenerator::DATA_TYPE_EMAIL,
                'allow_empty' => false,
            ],
            [
                'code' => 'url_field',
                'label' => 'URL',
                'input_type' => \FormGenerator::INPUT_TYPE_TEXT,
                'data_type' => \FormGenerator::DATA_TYPE_URL,
                'allow_empty' => false,
            ],
            [
                'code' => 'ip_field',
                'label' => 'IP地址',
                'input_type' => \FormGenerator::INPUT_TYPE_TEXT,
                'data_type' => \FormGenerator::DATA_TYPE_IP,
                'allow_empty' => false,
            ],
            [
                'code' => 'data_field',
                'label' => '日期',
                'input_type' => \FormGenerator::INPUT_TYPE_TEXT,
                'data_type' => \FormGenerator::DATA_TYPE_DATE,
                'allow_empty' => false,
            ],
            [
                'code' => 'phone_field',
                'label' => '宅电',
                'input_type' => \FormGenerator::INPUT_TYPE_TEXT,
                'data_type' => \FormGenerator::DATA_TYPE_PHONE,
                'allow_empty' => false,
            ],
            [
                'code' => 'fax_field',
                'label' => '传真号',
                'input_type' => \FormGenerator::INPUT_TYPE_TEXT,
                'data_type' => \FormGenerator::DATA_TYPE_FAX,
                'allow_empty' => false,
            ],
            [
                'code' => 'mobile_field',
                'label' => '手机',
                'input_type' => \FormGenerator::INPUT_TYPE_TEXT,
                'data_type' => \FormGenerator::DATA_TYPE_MOBILE,
                'allow_empty' => false,
            ],
            [
                'code' => 'contact_field',
                'label' => '联系人',
                'input_type' => \FormGenerator::INPUT_TYPE_TEXT,
                'data_type' => \FormGenerator::DATA_TYPE_CONTACT,
                'allow_empty' => false,
            ],
            [
                'code' => 'zip_field',
                'label' => '邮政编码',
                'input_type' => \FormGenerator::INPUT_TYPE_TEXT,
                'data_type' => \FormGenerator::DATA_TYPE_ZIP,
                'allow_empty' => false,
            ],
            [
                'code' => 'username_field',
                'label' => '用户名',
                'input_type' => \FormGenerator::INPUT_TYPE_TEXT,
                'data_type' => \FormGenerator::DATA_TYPE_USERNAME,
                'allow_empty' => false,
            ],
            [
                'code' => 'datetime_field',
                'label' => '时间',
                'input_type' => \FormGenerator::INPUT_TYPE_TEXT,
                'data_type' => \FormGenerator::DATA_TYPE_TIME,
                'allow_empty' => false,
            ],
            [
                'code' => 'password_field',
                'label' => '显示密码',
                'input_type' => \FormGenerator::INPUT_TYPE_TEXT,
                'data_type' => \FormGenerator::DATA_TYPE_PASSWORD,
                'allow_empty' => false,
            ],
            [
                'code' => 'compare_field',
                'label' => '确认密码',
                'input_type' => \FormGenerator::INPUT_TYPE_TEXT,
                'data_type' => \FormGenerator::DATA_TYPE_COMPARE,
                'compare_field' => 'password_field',
            ],
            [
                'code' => 'numeric_field',
                'label' => '数字',
                'input_type' => \FormGenerator::INPUT_TYPE_TEXT,
                'data_type' => \FormGenerator::DATA_TYPE_NUMERIC,
                'allow_empty' => false,
            ],
            [
                'code' => 'money_field',
                'label' => '货币',
                'input_type' => \FormGenerator::INPUT_TYPE_TEXT,
                'data_type' => \FormGenerator::DATA_TYPE_MONEY,
                'allow_empty' => false,
            ],
            [
                'code' => 'integer_field',
                'label' => '整数',
                'input_type' => \FormGenerator::INPUT_TYPE_TEXT,
                'data_type' => \FormGenerator::DATA_TYPE_INTEGER,
                'allow_empty' => false,
            ],
            [
                'code' => 'preg_field',
                'label' => '正则匹配',
                'input_type' => \FormGenerator::INPUT_TYPE_TEXT,
                'data_type' => \FormGenerator::DATA_TYPE_PREG,
                'pattern' => '/\d{2,4}/',
                'allow_empty' => false,
            ],
            [
                'code' => 'string_field',
                'label' => '字符串',
                'input_type' => \FormGenerator::INPUT_TYPE_TEXT,
                'data_type' => \FormGenerator::DATA_TYPE_STRING,
                'allow_empty' => false,
            ],
        ];
    }
}
```

### 1.2 组件表单渲染
```php
<?php $this->widget('\Widgets\FormGenerator', [
    'model' => $model,
]); ?>
```

## ====== 异常代码集合 ======

异常代码格式：1029 - XXX - XX （组件编号 - 文件编号 - 代码内异常）
```
 - 102900101 : "{model}"无效属性"{attribute}"
 - 102900102 : 对比验证必须标记"compare_field"属性
 - 102900103 : 匹配验证必须指定"pattern"属性
 - 102900104 : 对比验证必须标记"compare_field"属性
 - 102900105 : 选择验证必须设置"input_data"属性
 - 102900106 : 选择验证"input_data"属性必须为数组
 - 102900107 : 多选验证必须设置"input_data"属性
 - 102900108 : 选择验证"input_data"属性必须为数组
 - 102900109 : 单选验证必须设置"input_data"属性
 - 102900110 : 选择验证"input_data"属性必须为数组
 
 - 102900201 : 无效的"\Widgets\FormGenerator"属性设置
 - 102900202 : 对比验证必须标记"compare_field"属性
 - 102900203 : 匹配验证必须指定"pattern"属性
 - 102900204 : 对比验证必须标记"compare_field"属性
 - 102900205 : 需要设置属性"input_data"
 - 102900206 : 属性"input_data"必须是数组
```