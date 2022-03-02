<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php $this->showOrNull('title') ?></title>
    <?php $this->renderCssFiles();?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@200&display=swap" rel="stylesheet">
</head>
<body>



<div class="main-container">
    <header class="main-header">
        <div class="logo">
            <i class="fa-brands fa-php"></i>
            <span>My software</span>
        </div>
    </header>

    <div class="center-container">
        <aside class="side-menu">
            <ul>
                <li><a href="<?php $this->siteUrl('/home') ?>"><i class="fas fa-home"></i>Home</a></li>
                <li><a href="<?php $this->siteUrl('/salas') ?>"><i class="fas fa-home"></i>Gerenciamento de salas</a></li>
                <li><a href="<?php $this->siteUrl('/agendamento') ?>"><i class="fas fa-home"></i>Gerenciamento de agendamentos</a></li>
                <li><a href="<?php $this->siteUrl('/docs') ?>"><i class="fas fa-home"></i>Api doc</a></li>
            </ul>
        </aside>

        <main class="page-content">
            <div class="messages">
            <?php if(!empty($this->messages)) :?>
                <?php $this->renderMessages() ?>
            <?php endif;?>
            </div>
            <?php $this->renderPage(); ?>
        </main>
    </div>


    <footer class="main-footer">
        <p>Desenvolvido por Renan Salustiano - renansalustiano2020@gmail.com </p>
    </footer>
</div>
<script type="text/javascript" src="<?php $this->siteUrl('/public/js/jquery-3.6.0.min.js') ?>"></script>
<script src="https://kit.fontawesome.com/27e19d0aae.js" crossorigin="anonymous"></script>
<?php $this->renderJsFiles(); ?>
<?php $this->renderJsVars(); ?>
</body>
</html>