<?php
/**
 * Link         :   http://www.phpcorner.net
 * User         :   qingbing<780042175@qq.com>
 * Date         :   2019-01-23
 * Version      :   1.0
 */

class FormGenerator
{
    /**
     * 表单域的类型
     */
    const INPUT_TYPE_TEXT = 'text';
    const INPUT_TYPE_TEXTAREA = 'textarea';
    const INPUT_TYPE_SELECT = 'select'; // data-type => select,choice
    const INPUT_TYPE_CHECKBOX = 'checkbox'; // data-type => checked
    const INPUT_TYPE_CHECKBOX_LIST = 'checkbox_list'; // data => choice
    const INPUT_TYPE_RADIO_LIST = 'radio_list'; // data-type => checked
    const INPUT_TYPE_EDITOR = 'editor';
    const INPUT_TYPE_PASSWORD = 'password'; // data-type => password,compare
    const INPUT_TYPE_FILE = 'file';
    const INPUT_TYPE_HIDDEN = 'hidden';

    /**
     * jquery.validate的验证类型
     */
    const DATA_TYPE_REQUIRED = 'required';
    const DATA_TYPE_EMAIL = 'email';
    const DATA_TYPE_URL = 'url';
    const DATA_TYPE_IP = 'ip';
    const DATA_TYPE_PHONE = 'phone';
    const DATA_TYPE_MOBILE = 'mobile';
    const DATA_TYPE_CONTACT = 'contact';
    const DATA_TYPE_FAX = 'fax';
    const DATA_TYPE_ZIP = 'zip';
    const DATA_TYPE_TIME = 'time';
    const DATA_TYPE_DATE = 'date';
    const DATA_TYPE_USERNAME = 'username';
    const DATA_TYPE_PASSWORD = 'password';
    const DATA_TYPE_COMPARE = 'compare';
    const DATA_TYPE_PREG = 'preg';
    const DATA_TYPE_STRING = 'string';
    const DATA_TYPE_NUMERIC = 'numeric';
    const DATA_TYPE_INTEGER = 'integer';
    const DATA_TYPE_MONEY = 'money';
    const DATA_TYPE_FILE = 'file';
    const DATA_TYPE_SELECT = 'select';
    const DATA_TYPE_CHOICE = 'choice';
    const DATA_TYPE_CHECKED = 'checked';

    /**
     * jquery.validate 验证提示的显示方式，line：提示在同一行，否在在不同行
     */
    const VALID_TYPE_LINE = 'line';
    const VALID_TYPE_ROW = 'row';

    /**
     * 表单元素类型
     * @return array
     */
    static public function inputType()
    {
        return [
            self::INPUT_TYPE_TEXT => '文本框',
            self::INPUT_TYPE_TEXTAREA => '文本域',
            self::INPUT_TYPE_SELECT => '菜单',
            self::INPUT_TYPE_CHECKBOX => '复选框',
            self::INPUT_TYPE_CHECKBOX_LIST => '复选组',
            self::INPUT_TYPE_RADIO_LIST => '单选组',
            self::INPUT_TYPE_EDITOR => '编辑器',
            self::INPUT_TYPE_PASSWORD => '密码框',
            self::INPUT_TYPE_FILE => '文件域',
            self::INPUT_TYPE_HIDDEN => '隐藏框',
        ];
    }

    /**
     * 表单的验证类型
     * @return array
     */
    static public function dataType()
    {
        return [
            self::DATA_TYPE_REQUIRED => '必填',
            self::DATA_TYPE_EMAIL => '邮件',
            self::DATA_TYPE_URL => 'Url',
            self::DATA_TYPE_IP => 'IP',
            self::DATA_TYPE_PHONE => '宅电',
            self::DATA_TYPE_MOBILE => '手机',
            self::DATA_TYPE_CONTACT => '联系电话',
            self::DATA_TYPE_FAX => '传真',
            self::DATA_TYPE_ZIP => '邮编',
            self::DATA_TYPE_TIME => '时间',
            self::DATA_TYPE_DATE => '日期',
            self::DATA_TYPE_USERNAME => '用户名',
            self::DATA_TYPE_PASSWORD => '密码',
            self::DATA_TYPE_COMPARE => '确认对比',
            self::DATA_TYPE_PREG => '正在匹配',
            self::DATA_TYPE_STRING => '字符串',
            self::DATA_TYPE_NUMERIC => '数字',
            self::DATA_TYPE_INTEGER => '整数',
            self::DATA_TYPE_MONEY => '货币',
            self::DATA_TYPE_FILE => '文件上传',
            self::DATA_TYPE_SELECT => '单选',
            self::DATA_TYPE_CHOICE => '多选',
            self::DATA_TYPE_CHECKED => '勾选',
        ];
    }
}