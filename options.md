# 1. 公有属性
## 1.1 普通属性
- code : 属性标记，必填
- label : 属性标签，必填
- default : 属性默认值，选填，默认为空

## 1.2 规则属性
### 1.2.1 通用规则属性
- allow_empty : 允许为空，选填，默认允许
    - php => allowEmpty
    - jquery => allowEmpty
- error_msg : 自定义的错误消息，选填
    - php => message
    - jquery => errorMsg
- tip_msg : 前端提示信息，选填
    - jquery => tipMsg
- empty_msg ： 信息为空时提示
    - php => emptyMessage
    - jquery => emptyMsg

### 1.2.2 表单域属性
- input_type : 表单域类型，必填
    - text : input-text
    - textarea : textarea
    - select : select
    - checkbox : input-checkbox
    - checkbox_list : input-checkbox-group
    - radio_list : input-radio-group
    - editor : kindeditor
    - password : input-password
        - data_type : compare, password
    - file : input-file
    - hidden : input-hidden

### 1.2.2 验证类规则属性
- data_type : 数据验证类型，会寻找相应的jquery.validate和php.validate的验证规则，根据 input-type 选填
    - required :
    - email :
    - url :
    - ip :
    - phone :
    - mobile :
    - contact :
    - fax :
    - zip :
    - time :
    - date :
    - username :
    - password :
    - compare :
    - preg :
    - string :
    - numeric :
    - integer :
    - money :
    - file :
    - select :

### 1.2.3 个性化规则属性
- compare_field
    - php
        - compare => compareAttribute
    - jquery
        - compare => compare
- pattern
    - php
        - match => pattern

- input_data
    - php
        - in => range
        - multiIn => range
    - jquery

- min
    - php
        - \UploadFile::VALID_CLASS => minSize
        - string => minLength
        - numeric => min
        - money => min
        - integer => min

- min_msg
    - php
        - \UploadFile::VALID_CLASS => tooSmallMessage
        - string => tooShortMessage
        - numeric => tooSmallMessage
        - money => tooSmallMessage
        - integer => tooSmallMessage

- max
    - php
        - \UploadFile::VALID_CLASS => maxSize
        - string => maxLength
        - numeric => max
        - money => max
        - integer => max

- max_msg
    - php
        - \UploadFile::VALID_CLASS => tooLargeMessage
        - string => tooLongMessage
        - numeric => tooBigMessage
        - money => tooBigMessage
        - integer => tooBigMessage
- is_inline
    - 当 输入类型为 input-type