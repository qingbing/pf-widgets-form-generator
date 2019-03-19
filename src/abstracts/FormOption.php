<?php
/**
 * Link         :   http://www.phpcorner.net
 * User         :   qingbing<780042175@qq.com>
 * Date         :   2019-01-23
 * Version      :   1.0
 */

namespace Abstracts;


use Helper\Coding;
use Helper\Exception;

abstract class FormOption extends FormModel
{
    /* @var array 模型的属性集合 */
    private $_attributes = [];
    /* @var array 属性默认值 */
    private $_defaults;
    /* @var array 属性的名称 */
    private $_attributeNames;

    /**
     * 定义的表单项目
     * @return mixed
     */
    abstract public function getOptions();

    /**
     * 返回模型的属性名称列表
     * @return array
     */
    public function attributeNames()
    {
        if (null === $this->_attributeNames) {
            $R = [];
            foreach ($this->getOptions() as $op) {
                array_push($R, $op['code']);
            }
            $this->_attributeNames = $R;
        }
        return $this->_attributeNames;
    }

    /**
     * 获取属性显示label
     * @return array
     */
    final public function attributeLabels()
    {
        $ops = $this->getOptions();
        $R = [];
        foreach ($ops as $op) {
            $R[$op['code']] = $op['label'];
        }
        return $R;
    }

    /**
     * 获取选项默认值
     * @param string $attribute
     * @return array|null
     */
    protected function getDefaultValue($attribute)
    {
        if (null === $this->_defaults) {
            $defaults = [];
            foreach ($this->getOptions() as $op) {
                $defaults[$op['code']] = isset($op['default']) ? $op['default'] : '';
            }
            $this->_defaults = $defaults;
        }
        if (null === $attribute) {
            return $this->_defaults;
        } elseif (isset($this->_defaults[$attribute])) {
            return $this->_defaults[$attribute];
        }
        return null;
    }

    /**
     * 返回指定属性的值
     * @param string $name
     * @return mixed
     */
    public function getAttribute($name)
    {
        if (property_exists($this, $name)) {
            return $this->{$name};
        } else if (isset($this->_attributes[$name])) {
            return $this->_attributes[$name];
        }
        return $this->getDefaultValue($name);
    }

    /**
     * 设定属性的值
     * @param string $name
     * @param mixed $value
     * @throws Exception
     */
    public function setAttribute($name, $value)
    {
        if (property_exists($this, $name)) {
            $this->{$name} = $value;
        } else if (in_array($name, $this->attributeNames())) {
            $this->_attributes[$name] = $value;
        } else {
            throw new Exception(str_cover('"{model}"无效属性"{attribute}"', [
                'model' => get_class($this),
                '{attribute}' => $name,
            ]), 102900101);
        }
    }

    /**
     * 制作表单项目的后台验证规则
     * @return array
     * @throws Exception
     */
    final public function rules()
    {
        $R = [];
        foreach ($this->getOptions() as $op) {
            $tmp = [];
            array_push($tmp, $op['code']);
            switch ($op['input_type']) {
                case \FormGenerator::INPUT_TYPE_HIDDEN:
                case \FormGenerator::INPUT_TYPE_TEXT:
                case \FormGenerator::INPUT_TYPE_TEXTAREA:
                    switch ($op['data_type']) {
                        case \FormGenerator::DATA_TYPE_REQUIRED:
                            array_push($tmp, 'string');
                            $op['allow_empty'] = false;
                            break;
                        case \FormGenerator::DATA_TYPE_EMAIL:
                        case \FormGenerator::DATA_TYPE_URL:
                        case \FormGenerator::DATA_TYPE_IP:
                        case \FormGenerator::DATA_TYPE_DATE:
                        case \FormGenerator::DATA_TYPE_PHONE:
                        case \FormGenerator::DATA_TYPE_FAX:
                        case \FormGenerator::DATA_TYPE_MOBILE:
                        case \FormGenerator::DATA_TYPE_CONTACT:
                        case \FormGenerator::DATA_TYPE_ZIP:
                        case \FormGenerator::DATA_TYPE_USERNAME:
                            array_push($tmp, $op['data_type']);
                            break;
                        case \FormGenerator::DATA_TYPE_TIME:
                            array_push($tmp, 'datetime');
                            break;
                        case \FormGenerator::DATA_TYPE_COMPARE:
                            array_push($tmp, 'compare');
                            if (!isset($op['compare_field']) || empty($op['compare_field'])) {
                                throw new Exception('对比验证必须标记"compare_field"属性', 102900102);
                            }
                            $tmp['compareAttribute'] = $op['compare_field'];
                            break;
                        case \FormGenerator::DATA_TYPE_PASSWORD:
                            array_push($tmp, 'password');
                            break;
                        case \FormGenerator::DATA_TYPE_NUMERIC:
                        case \FormGenerator::DATA_TYPE_MONEY:
                            array_push($tmp, 'numerical');
                            $tmp['integerOnly'] = false;
                            if (isset($op['min']) && is_numeric($op['min'])) {
                                $tmp['min'] = $op['min'];
                            }
                            if (isset($op['max']) && is_numeric($op['max'])) {
                                $tmp['max'] = $op['max'];
                            }
                            if (isset($op['min_msg']) && !empty($op['min_msg'])) {
                                $tmp['tooSmallMessage'] = $op['min_msg'];
                            }
                            if (isset($op['max_msg']) && !empty($op['max_msg'])) {
                                $tmp['tooBigMessage'] = $op['max_msg'];
                            }
                            break;
                        case \FormGenerator::DATA_TYPE_INTEGER:
                            array_push($tmp, 'numerical');
                            $tmp['integerOnly'] = true;
                            if (isset($op['min']) && is_numeric($op['min'])) {
                                $tmp['min'] = $op['min'];
                            }
                            if (isset($op['max']) && is_numeric($op['max'])) {
                                $tmp['max'] = $op['max'];
                            }
                            if (isset($op['min_msg']) && !empty($op['min_msg'])) {
                                $tmp['tooSmallMessage'] = $op['min_msg'];
                            }
                            if (isset($op['max_msg']) && !empty($op['max_msg'])) {
                                $tmp['tooBigMessage'] = $op['max_msg'];
                            }
                            break;
                        case \FormGenerator::DATA_TYPE_PREG:
                            array_push($tmp, 'match');
                            if (!isset($op['pattern']) || empty($op['pattern'])) {
                                throw new Exception('匹配验证必须指定"pattern"属性', 102900103);
                            }
                            $tmp['pattern'] = $op['pattern'];
                            break;
                        case \FormGenerator::DATA_TYPE_STRING:
                        default:
                            array_push($tmp, 'string');
                            if (isset($op['min']) && is_numeric($op['min']) && $op['min'] > 0) {
                                $tmp['minLength'] = $op['min'];
                            }
                            if (isset($op['max']) && is_numeric($op['max']) && $op['max'] > 0) {
                                $tmp['maxLength'] = $op['max'];
                            }
                            if (isset($op['min_msg']) && !empty($op['min_msg'])) {
                                $tmp['tooShortMessage'] = $op['min_msg'];
                            }
                            if (isset($op['max_msg']) && !empty($op['max_msg'])) {
                                $tmp['tooLongMessage'] = $op['max_msg'];
                            }
                            break;
                    }
                    break;
                case \FormGenerator::INPUT_TYPE_PASSWORD:
                    if ($op['data_type'] == \FormGenerator::DATA_TYPE_COMPARE) {
                        array_push($tmp, 'compare');
                        if (!isset($op['compare_field']) || empty($op['compare_field'])) {
                            throw new Exception('对比验证必须标记"compare_field"属性', 102900104);
                        }
                        $tmp['compareAttribute'] = $op['compare_field'];
                    } else {
                        array_push($tmp, 'password');
                    }
                    break;
                case \FormGenerator::INPUT_TYPE_SELECT:
                    if (isset($op['data_type']) && $op['data_type'] === \FormGenerator::DATA_TYPE_CHOICE) {
                        array_push($tmp, 'multiIn');
                    } else {
                        array_push($tmp, 'in');
                    }
                    if (!isset($op['input_data']) || empty($op['input_data'])) {
                        throw new Exception('选择验证必须设置"input_data"属性', 102900105);
                    }
                    if (!is_array($op['input_data'])) {
                        $op['input_data'] = array_keys(Coding::json_decode($op['input_data'], true));
                    }
                    if (!is_array($op['input_data']) || empty($op['input_data'])) {
                        throw new Exception('选择验证"input_data"属性必须为数组', 102900106);
                    }
                    $tmp['range'] = $op['input_data'];
                    break;
                case \FormGenerator::INPUT_TYPE_CHECKBOX:
                    array_push($tmp, 'boolean');
                    break;
                case \FormGenerator::INPUT_TYPE_CHECKBOX_LIST:
                    array_push($tmp, 'multiIn');
                    if (!isset($op['input_data']) || empty($op['input_data'])) {
                        throw new Exception('多选验证必须设置"input_data"属性', 102900107);
                    }
                    if (!is_array($op['input_data'])) {
                        $op['input_data'] = array_keys(Coding::json_decode($op['input_data'], true));
                    }
                    if (!is_array($op['input_data']) || empty($op['input_data'])) {
                        throw new Exception('选择验证"input_data"属性必须为数组', 102900108);
                    }
                    $tmp['range'] = $op['input_data'];
                    break;
                case \FormGenerator::INPUT_TYPE_RADIO_LIST:
                    array_push($tmp, 'in');
                    if (!isset($op['input_data']) || empty($op['input_data'])) {
                        throw new Exception('单选验证必须设置"input_data"属性', 102900109);
                    }
                    if (!is_array($op['input_data'])) {
                        $op['input_data'] = array_keys(Coding::json_decode($op['input_data'], true));
                    }
                    if (!is_array($op['input_data']) || empty($op['input_data'])) {
                        throw new Exception('选择验证"input_data"属性必须为数组', 102900110);
                    }
                    $tmp['range'] = $op['input_data'];
                    break;
                case \FormGenerator::INPUT_TYPE_FILE:
                    array_push($tmp, \UploadFile::VALID_CLASS);
                    if (isset($op['file_extensions'])) {
                        if (!is_array($op['file_extensions'])) {
                            $op['file_extensions'] = array_map('trim', explode('|', $op['file_extensions']));;
                        }
                        if (!empty($op['file_extensions'])) {
                            $tmp['types'] = $op['file_extensions'];
                        }
                    }
                    if (isset($op['min']) && is_numeric($op['min']) && $op['min'] > 0) {
                        $tmp['minSize'] = $op['min']; // 文件上传的最小字节数
                    }
                    if (isset($op['max']) && is_numeric($op['max']) && $op['max'] > 0) {
                        $tmp['maxSize'] = $op['max']; // 文件上传的最大字节数
                    }
                    if (isset($op['min_msg']) && !empty($op['min_msg'])) {
                        $tmp['tooSmallMessage'] = $op['min_msg'];
                    }
                    if (isset($op['max_msg']) && !empty($op['max_msg'])) {
                        $tmp['tooLargeMessage'] = $op['max_msg'];
                    }
                    break;
                case \FormGenerator::INPUT_TYPE_EDITOR:
                default :
                    array_push($tmp, 'string');
                    if (isset($op['min']) && is_numeric($op['min']) && $op['min'] > 0) {
                        $tmp['minLength'] = $op['min'];
                    }
                    if (isset($op['max']) && is_numeric($op['max']) && $op['max'] > 0) {
                        $tmp['maxLength'] = $op['max'];
                    }
                    if (isset($op['min_msg']) && !empty($op['min_msg'])) {
                        $tmp['tooShortMessage'] = $op['min_msg'];
                    }
                    if (isset($op['max_msg']) && !empty($op['max_msg'])) {
                        $tmp['tooLongMessage'] = $op['max_msg'];
                    }
                    break;
            }

            // 公共配置规则处理
            if (isset($op['allow_empty']) && !$op['allow_empty']) {
                $tmp['allowEmpty'] = false;
            } else {
                $tmp['allowEmpty'] = true;
            }
            if (isset($op['error_msg']) && !empty($op['error_msg'])) {
                $tmp['message'] = $op['error_msg'];
            }
            if (isset($op['empty_msg']) && $op['empty_msg']) {
                $tmp['emptyMessage'] = $op['empty_msg'];
            }
            array_push($R, $tmp);
        }
        return $R;
    }

    /**
     * __get：魔术方法，当直接访问属性不存在时被唤醒
     * @param string $property
     * @return mixed
     * @throws \Helper\Exception
     */
    public function __get($property)
    {
        if (null !== ($value = $this->getAttribute($property))) {
            return $value;
        }
        return parent::__get($property);
    }

    /**
     * __set：魔术方法，当直接设置不存在属性时被唤醒
     * @param string $name
     * @param mixed $value
     * @throws \Helper\Exception
     */
    public function __set($name, $value)
    {
        if (false === $this->setAttribute($name, $value)) {
            parent::__set($name, $value);
        }
    }
}