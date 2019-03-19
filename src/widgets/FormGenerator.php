<?php
/**
 * Link         :   http://www.phpcorner.net
 * User         :   qingbing<780042175@qq.com>
 * Date         :   2019-01-23
 * Version      :   1.0
 */

namespace Widgets;


use Abstracts\FormOption;
use Abstracts\OutputProcessor;
use Helper\Coding;
use Helper\Exception;
use Html;

class FormGenerator extends OutputProcessor
{
    /* @var FormOption */
    public $model;
    /* @var array 项目选项 */
    public $options;
    /* @var string 验证提示的显示方式，line：提示在同一行，否在在不同行 */
    public $validType = 'line';

    private $_htmlOptions = [];
    private $_options;

    /**
     * 组件自动调用
     * @throws Exception
     */
    public function init()
    {
        if (empty($this->options) && !$this->model instanceof FormOption) {
            throw new Exception('无效的"\Widgets\FormGenerator"属性设置', 102900201);
        }
    }

    /**
     * 返回真正的输出项目
     * @return array
     */
    public function getOptions()
    {
        if (null === $this->_options) {
            if (!empty($this->options)) {
                $ops = $this->options;
            } else {
                $ops = $this->model->getOptions();
            }
            $R = [];
            foreach ($ops as $code => $op) {
                if (is_string($op)) {
                    $R[$op] = $op;
                } else if (is_string($code)) {
                    $op['code'] = $code;
                    $R[$code] = $op;
                } else {
                    $R[$op['code']] = $op;
                }
            }
            $this->_options = $R;
        }
        return $this->_options;
    }

    /**
     * 运行组件
     * @throws \Exception
     */
    public function run()
    {
        foreach ($this->getOptions() as $code => $op) {
            if (is_string($op)) {
                echo $this->generateViewOption($op, []) . "\n";
                continue;
            }
            if (isset($op['type']) && 'view' == $op['type']) {
                echo $this->generateViewOption($op['code'], $op) . "\n";
                continue;
            }
            echo $this->generateFormOption($op) . "\n";
        }
    }

    /**
     * 获取表单显示项
     * @param string $property
     * @param array $op
     * @return string
     * @throws Exception
     */
    protected function generateViewOption($property, $op = [])
    {
        $model = $this->model;
        $label = Html::activeLabel($model, $property);
        if (isset($op['callable'])) {
            $html = call_user_func_array($op['callable'], [$model->{$property}]);
        } else if (isset($op['function'])) {
            $html = call_user_func_array($op['function'], [$model, $property]);
        } else if (isset($op['html']) && $op['html']) {
            $html = $model->{$property};
        } else {
            $html = Html::encode($model->{$property});
        }
        return <<<EDO
<dl class="form-group row">
    <dt class="col-md-3 col-sm-3 col-lg-3 control-label">{$label}:</dt>
    <dd class="col-md-9 col-sm-9 col-lg-9 form-control-static">{$html}</dd>
</dl>
EDO;
    }

    /**
     * 获取表单验证项的显示HTML
     * @param array $op
     * @return string
     * @throws \Exception
     */
    protected function generateFormOption($op)
    {
        // 属性label
        $label = Html::activeLabel($this->model, $op['code']);
        // 表单的属性
        $htmlOptions = $this->getHtmlOption($op['code']);
        $helpBlockId = null;
        if ('line' === $this->validType) {
            Html::resolveNameId($this->model, $op['code'], $htmlOptions);
            $helpBlockId = 'help-block_' . $htmlOptions['id'];
            $htmlOptions['data-help-block'] = "#{$helpBlockId}";
        }
        // 表单html
        switch ($op['input_type']) {
            case \FormGenerator::INPUT_TYPE_TEXT:
                $this->addHtmlClass($htmlOptions, 'form-control');
                $html = Html::activeTextField($this->model, $op['code'], $htmlOptions);
                break;
            case \FormGenerator::INPUT_TYPE_TEXTAREA:
                $this->addHtmlClass($htmlOptions, 'form-control');
                $html = Html::activeTextArea($this->model, $op['code'], $htmlOptions);
                break;
            case \FormGenerator::INPUT_TYPE_SELECT:
                $this->addHtmlClass($htmlOptions, 'form-control');
                $inputData = $this->getInputData($op);
                if (isset($op['data_type']) && $op['data_type'] === \FormGenerator::DATA_TYPE_CHOICE) {
                    $size = count($inputData);
                    if ($size > 4) {
                        $htmlOptions['size'] = 4;
                    } else {
                        $htmlOptions['size'] = $size;
                    }
                }
                $html = Html::activeDropDownList($this->model, $op['code'], $inputData, $htmlOptions);
                break;
            case \FormGenerator::INPUT_TYPE_CHECKBOX:
                $html = '<div class="checkbox"><label>' . Html::activeCheckBox($this->model, $op['code'], $htmlOptions) . $label . '</label></div>';
                $label = '';
                break;
            case \FormGenerator::INPUT_TYPE_CHECKBOX_LIST:
                $htmlOptions['separator'] = '';
                if (isset($htmlOptions['is_inline'])) {
                    $template = '<div class="checkbox checkbox-inline">{input} {label}</div>';
                } else {
                    $template = '<div class="checkbox"><label>{input} {label}</label></div>';
                }
                $htmlOptions['template'] = $template;
                $html = Html::activeCheckBoxList($this->model, $op['code'], $this->getInputData($op), $htmlOptions);
                $html = '<div>' . $html . '</div>';
                break;
            case \FormGenerator::INPUT_TYPE_RADIO_LIST:
                $htmlOptions['separator'] = '';
                if (isset($htmlOptions['is_inline'])) {
                    $template = '<div class="radio radio-inline">{input} {label}</div>';
                } else {
                    $template = '<div class="radio"><label>{input} {label}</label></div>';
                }
                $htmlOptions['template'] = $template;
                $html = Html::activeRadioButtonList($this->model, $op['code'], $this->getInputData($op), $htmlOptions);
                $html = '<div>' . $html . '</div>';
                break;
            case \FormGenerator::INPUT_TYPE_EDITOR:
                $attr = [
                    'model' => $this->model,
                    'contentField' => $op['code'],
                    'htmlOptions' => $htmlOptions,
                ];
                if (isset($op['editor'])) {
                    $attr = array_merge($op['editor'], $attr);
                } else {
                    $attr['openFlag'] = false;
                    $attr['mode'] = 'mini';
                }
                $html = \PF::app()->getController()->widget('\Widgets\KindEditor', $attr, true);
                break;
            case \FormGenerator::INPUT_TYPE_PASSWORD:
                $this->addHtmlClass($htmlOptions, 'form-control');
                $html = Html::activePasswordField($this->model, $op['code'], $htmlOptions);
                break;
            case \FormGenerator::INPUT_TYPE_FILE:
                $this->addHtmlClass($htmlOptions, 'form-control');
                $html = Html::activeFileField($this->model, $op['code'], $htmlOptions);
                break;
            case \FormGenerator::INPUT_TYPE_HIDDEN:
                $html = Html::activeHiddenField($this->model, $op['code'], $htmlOptions);
                break;
            default:
                $this->addHtmlClass($htmlOptions, 'form-control');
                $html = Html::activeTextField($this->model, $op['code'], $htmlOptions);
                break;
        }
        if ($op['input_type'] == \FormGenerator::INPUT_TYPE_HIDDEN) {
            return "{$html}";
        }
        if (!empty($label)) {
            $label .= '：';
        }
        if (null !== $helpBlockId) {
            return <<<EOD
<dl class="form-group row">
    <dt class="col-md-3 col-sm-3 col-lg-3 control-label">{$label}</dt>
    <dd class="col-md-6 col-sm-6 col-lg-6">{$html}</dd>
    <dd class="col-sm-3 col-md-3 col-lg-3 text-left" id="{$helpBlockId}"></dd>
</dl>
EOD;
        } else {
            return <<<EOD
<dl class="form-group row">
    <dt class="col-md-3 col-sm-3 col-lg-3 control-label">{$label}</dt>
    <dd class="col-md-9 col-sm-9 col-lg-9">{$html}</dd>
</dl>
EOD;
        }
    }

    /**
     * 获取具体属性的html属性
     * @param string $code
     * @return array
     * @throws Exception
     */
    protected function getHtmlOption($code)
    {
        if (!isset($this->_htmlOptions[$code])) {
            $op = $this->getOptions()[$code];
            $htmlOptions = [];
            if (isset($op['css_id']) && $op['css_id']) {
                $htmlOptions['id'] = $op['css_id'];
            }
            if (isset($op['css_class']) && $op['css_class']) {
                $htmlOptions['class'] = $op['css_class'];
            }
            if (isset($op['css_style']) && $op['css_style']) {
                $htmlOptions['style'] = $op['css_style'];
            }
            if (isset($op['allow_empty']) && !$op['allow_empty']) {
                $htmlOptions['data-allow-empty'] = "false";
            } else {
                $htmlOptions['data-allow-empty'] = "true";
            }
            if (isset($op['callback']) && $op['callback']) {
                $htmlOptions['data-callback'] = $op['callback'];
            }
            if (isset($op['ajax_url']) && $op['ajax_url']) {
                $htmlOptions['data-ajax-url'] = $op['ajax_url'];
            }
            if (isset($op['tip_msg']) && $op['tip_msg']) {
                $htmlOptions['data-tip-msg'] = $op['tip_msg'];
            }
            if (isset($op['empty_msg']) && $op['empty_msg']) {
                $htmlOptions['data-empty-msg'] = $op['empty_msg'];
            }
            if (isset($op['error_msg']) && $op['error_msg']) {
                $htmlOptions['data-error-msg'] = $op['error_msg'];
            }

            switch ($op['input_type']) {
                case \FormGenerator::INPUT_TYPE_TEXT:
                case \FormGenerator::INPUT_TYPE_TEXTAREA:
                    switch ($op['data_type']) {
                        case \FormGenerator::DATA_TYPE_REQUIRED:
                            $htmlOptions['data-valid-type'] = 'required';
                            $htmlOptions['data-allow-empty'] = "false";
                            break;
                        case \FormGenerator::DATA_TYPE_EMAIL:
                        case \FormGenerator::DATA_TYPE_URL:
                        case \FormGenerator::DATA_TYPE_IP:
                        case \FormGenerator::DATA_TYPE_PHONE:
                        case \FormGenerator::DATA_TYPE_FAX:
                        case \FormGenerator::DATA_TYPE_MOBILE:
                        case \FormGenerator::DATA_TYPE_CONTACT:
                        case \FormGenerator::DATA_TYPE_USERNAME:
                            $htmlOptions['data-valid-type'] = $op['data_type'];
                            break;
                        case \FormGenerator::DATA_TYPE_ZIP:
                            $htmlOptions['data-valid-type'] = 'zipcode';
                            break;
                        case \FormGenerator::DATA_TYPE_DATE:
                            $htmlOptions['data-valid-type'] = 'date';
                            // 接入前端日历插件
                            $htmlOptions['class'] = 'w-dateRange';
                            $htmlOptions['data-time'] = 'false';
                            break;
                        case \FormGenerator::DATA_TYPE_TIME:
                            $htmlOptions['data-valid-type'] = 'date';
                            // 接入前端日历插件
                            $htmlOptions['class'] = 'w-dateRange';
                            $htmlOptions['data-time'] = 'true';
                            break;
                        case \FormGenerator::DATA_TYPE_PASSWORD:
                            $htmlOptions['data-valid-type'] = 'password';
                            break;
                        case \FormGenerator::DATA_TYPE_COMPARE:
                            $htmlOptions['data-valid-type'] = 'compare';
                            if (!isset($op['compare_field']) || empty($op['compare_field'])) {
                                throw new Exception('对比验证必须标记"compare_field"属性', 102900202);
                            }
                            $compareOptions = $this->getHtmlOption($op['compare_field']);
                            Html::resolveNameId($this->model, $op['compare_field'], $compareOptions);
                            $htmlOptions['data-compare'] = '#' . $compareOptions['id'];
                            break;
                        case \FormGenerator::DATA_TYPE_PREG:
                            $htmlOptions['data-valid-type'] = 'preg';
                            if (!isset($op['pattern']) || empty($op['pattern'])) {
                                throw new Exception('匹配验证必须指定"pattern"属性', 102900203);
                            }
                            $htmlOptions['data-pattern'] = $op['pattern'];
                            break;
                        case \FormGenerator::DATA_TYPE_STRING:
                            $htmlOptions['data-valid-type'] = $op['data_type'];
                            if (isset($op['min']) && is_numeric($op['min'])) {
                                $htmlOptions['data-min-length'] = $op['min'];
                            }
                            if (isset($op['max']) && is_numeric($op['max'])) {
                                $htmlOptions['data-max-length'] = $op['max'];
                            }
                            if (isset($op['min_msg']) && !empty($op['min_msg'])) {
                                $htmlOptions['data-min-error-msg'] = $op['min_msg'];
                            }
                            if (isset($op['max_msg']) && !empty($op['max_msg'])) {
                                $htmlOptions['data-max-error-msg'] = $op['max_msg'];
                            }
                            break;
                        case \FormGenerator::DATA_TYPE_NUMERIC:
                        case \FormGenerator::DATA_TYPE_INTEGER:
                        case \FormGenerator::DATA_TYPE_MONEY:
                            $htmlOptions['data-valid-type'] = $op['data_type'];
                            if (isset($op['min']) && is_numeric($op['min'])) {
                                $htmlOptions['data-min'] = $op['min'];
                            }
                            if (isset($op['max']) && is_numeric($op['max'])) {
                                $htmlOptions['data-max'] = $op['max'];
                            }
                            if (isset($op['min_msg']) && !empty($op['min_msg'])) {
                                $htmlOptions['data-min-error-msg'] = $op['min_msg'];
                            }
                            if (isset($op['max_msg']) && !empty($op['max_msg'])) {
                                $htmlOptions['data-max-error-msg'] = $op['max_msg'];
                            }
                            break;
                    }
                    break;
                case \FormGenerator::INPUT_TYPE_SELECT:
                    if (isset($op['data_type']) && $op['data_type'] === \FormGenerator::DATA_TYPE_CHOICE) {
                        $htmlOptions['data-valid-type'] = 'choice';
                        $htmlOptions['multiple'] = 'multiple';
                    } else {
                        $htmlOptions['data-valid-type'] = 'select';
                    }
                    if (isset($op['min']) && is_numeric($op['min'])) {
                        $htmlOptions['data-min'] = $op['min'];
                    }
                    if (isset($op['max']) && is_numeric($op['max'])) {
                        $htmlOptions['data-max'] = $op['max'];
                    }
                    if (isset($op['min_msg']) && !empty($op['min_msg'])) {
                        $htmlOptions['data-min-error-msg'] = $op['min_msg'];
                    }
                    if (isset($op['max_msg']) && !empty($op['max_msg'])) {
                        $htmlOptions['data-max-error-msg'] = $op['max_msg'];
                    }
                    break;
                case \FormGenerator::INPUT_TYPE_CHECKBOX:
                    $htmlOptions['data-valid-type'] = 'checked';
                    break;
                case \FormGenerator::INPUT_TYPE_CHECKBOX_LIST:
                    $htmlOptions['data-valid-type'] = 'choice';
                    if (isset($op['min']) && is_numeric($op['min'])) {
                        $htmlOptions['data-min'] = $op['min'];
                    }
                    if (isset($op['max']) && is_numeric($op['max'])) {
                        $htmlOptions['data-max'] = $op['max'];
                    }
                    if (isset($op['min_msg']) && !empty($op['min_msg'])) {
                        $htmlOptions['data-min-error-msg'] = $op['min_msg'];
                    }
                    if (isset($op['max_msg']) && !empty($op['max_msg'])) {
                        $htmlOptions['data-max-error-msg'] = $op['max_msg'];
                    }
                    break;
                case \FormGenerator::INPUT_TYPE_RADIO_LIST:
                    $htmlOptions['data-valid-type'] = 'checked';
                    break;
                case \FormGenerator::INPUT_TYPE_EDITOR:
                    $htmlOptions['data-valid-type'] = 'string';
                    break;
                case \FormGenerator::INPUT_TYPE_PASSWORD:
                    $htmlOptions['data-valid-type'] = 'password';
                    if (isset($op['data_type']) && \FormGenerator::DATA_TYPE_COMPARE == $op['data_type']) {
                        $htmlOptions['data-valid-type'] = 'compare';
                        if (!isset($op['compare_field']) || empty($op['compare_field'])) {
                            throw new Exception('对比验证必须标记"compare_field"属性', 102900204);
                        }
                        $compareOptions = $this->getHtmlOption($op['compare_field']);
                        Html::resolveNameId($this->model, $op['compare_field'], $compareOptions);
                        $htmlOptions['data-compare'] = '#' . $compareOptions['id'];
                    }
                    break;
                case \FormGenerator::INPUT_TYPE_FILE:
                    $htmlOptions['data-valid-type'] = 'file';
                    if (isset($op['file_extensions'])) {
                        if (!is_array($op['file_extensions'])) {
                            $op['file_extensions'] = array_map('trim', explode('|', $op['file_extensions']));;
                        }
                        if (!empty($op['file_extensions'])) {
                            $htmlOptions['data-suffix'] = Coding::json_encode($op['file_extensions']);
                        }
                    }
                    $htmlOptions['class'] = 'w-input-file'; // 接入前端文件上传插件
                    break;
                case \FormGenerator::INPUT_TYPE_HIDDEN:
                    break;
                default:
                    $htmlOptions['data-valid-type'] = 'string';
                    if (isset($op['min']) && is_numeric($op['min'])) {
                        $htmlOptions['data-min-length'] = $op['min'];
                    }
                    if (isset($op['max']) && is_numeric($op['max'])) {
                        $htmlOptions['data-max-length'] = $op['max'];
                    }
                    if (isset($op['min_msg']) && !empty($op['min_msg'])) {
                        $htmlOptions['data-min-error-msg'] = $op['min_msg'];
                    }
                    if (isset($op['max_msg']) && !empty($op['max_msg'])) {
                        $htmlOptions['data-max-error-msg'] = $op['max_msg'];
                    }
                    break;
            }
            $this->_htmlOptions[$code] = $htmlOptions;
        }
        return $this->_htmlOptions [$code];
    }

    /**
     * 获取选择项的选项
     * @param array $op
     * @return array
     * @throws Exception
     */
    protected function getInputData($op)
    {
        if (!isset($op['input_data']) || empty($op['input_data'])) {
            throw new Exception('需要设置属性"input_data"', 102900205);
        }
        if (!is_array($op['input_data'])) {
            $op['input_data'] = Coding::json_decode($op['input_data'], true);
        }
        if (!is_array($op['input_data']) || empty($op['input_data'])) {
            throw new Exception('属性"input_data"必须是数组', 102900206);
        }
        return $op['input_data'];
    }

    /**
     * 配合前端的jquery.validate.js和样式，添加必要的css类名
     * @param array $htmlOptions
     * @param $cssClass
     */
    protected function addHtmlClass(array &$htmlOptions, $cssClass)
    {
        if (isset($htmlOptions['class'])) {
            $htmlOptions['class'] .= " {$cssClass}";
        } else {
            $htmlOptions['class'] = $cssClass;
        }
    }
}