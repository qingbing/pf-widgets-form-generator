<?php $baseUrl = $this->getApp()->getRequest()->getBaseUrl(); ?><!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="<?php echo $baseUrl . "/assets/"; ?>css/main.css">
    <link rel="stylesheet" href="<?php echo $baseUrl . "/assets/"; ?>css/plugins.css">
    <link rel="stylesheet" href="<?php echo $baseUrl . "/assets/"; ?>css/site.css">
    <script src="<?php echo $baseUrl . "/assets/"; ?>js/jquery-3.2.1.min.js"></script>
    <script src="<?php echo $baseUrl . "/assets/"; ?>js/holder.min.js"></script>
    <script src="<?php echo $baseUrl . "/assets/"; ?>js/h.js"></script>
    <script src="<?php echo $baseUrl . "/assets/"; ?>js/autoload.js"></script>
    <script src="<?php echo $baseUrl . "/assets/"; ?>js/common.js"></script>
    <title>Sub Model</title>
</head>
<body>
<div class="container">
    <h3 class="page-header">Form Generator Widget</h3>

    <!--data-modal-reload="true"  关闭modal后，主界面会重新加载  -->
    <!--data-reload="true"  正常提交后，本页面会重新加载  -->
    <!--data-modal-nothing="true"  关闭modal后，主界面不会有任何变化  -->
    <form class="form-horizontal w-validate" action="do.php"
          data-modal-reload="true"
          data-reload="true"
          data-modal-nothing="true"
          data-callback="PL.saveModalCallback">

        <?php $this->widget('\Widgets\FormGenerator', [
            'model' => $model,
        ]); ?>

        <dl class="form-group row">
            <dd class="col-sm-9 col-md-9 col-lg-9 col-sm-offset-3 col-md-offset-3 col-lg-offset-3">
                <button type="button" id="validateBtn" class="btn btn-info">validateBtn</button>
                <button type="submit" id="submitBtn" class="btn btn-primary">submitBtn</button>
                <button type="reset" id="resetBtn" class="btn btn-warning">resetBtn</button>
            </dd>
        </dl>
    </form>
</div>
</body>
</html>