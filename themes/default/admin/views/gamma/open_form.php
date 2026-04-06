<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header">
                <h2 class="blue"><i class="fa fa-file-text-o"></i><span class="break"></span><?= htmlspecialchars($form->form_title, ENT_QUOTES, 'UTF-8'); ?></h2>
            </div>
            <div class="box-content">
                <?= $generated_form_html; ?>
            </div>
        </div>
    </div>
</div>
