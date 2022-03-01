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
        <?php if($logged):?>
        <div class="user-options">
            <div class="button">
            <span><?php echo $_USER['username']; ?></span>
            </div>
            <ul>
               <li><a href="<?php $this->siteUrl('/logout')?>">Sair</a></li>
            </ul>
        </div>
        <?php endif;?>
    </header>

    <div class="center-container">
        <?php if($logged) :?>
        <aside class="side-menu">
            <ul>

                <?php if($_USER['access'] == "ADM"):?>
                <li><a href="<?php $this->siteUrl('/usuarios') ?>"><i class="fas fa-table"></i>Listagem Usuarios</a></li>
                <li><a href="<?php $this->siteUrl('/usuarios/form') ?>"><i class="fas fa-plus"></i>Registro de usuários</a></li>
                <?php endif;?>

                <li><a href="<?php $this->siteUrl('/funcionarios') ?>"><i class="fas fa-users"></i>Listagem de Funcionários</a></li>
                <li><a href="<?php $this->siteUrl('/funcionarios/form') ?>"><i class="fas fa-plus"></i>Registro de funcionários</a></li>
                <li><a href="<?php $this->siteUrl('/ponto') ?>"><i class="fas fa-clock"></i>Listagem de pontos de funcionários</a></li>
                <li><a href="<?php $this->siteUrl('/ponto/form') ?>"><i class="fas fa-clock"></i>Registro de ponto de funcionario</a></li>

            </ul>
        </aside>
        <?php endif;?>


        <main class="page-content <?php echo ($logged ?  'logged' : ''); ?>">
            <?php if(!empty($this->messages)) :?>
            <div class="messages">
                <?php $this->renderMessages() ?>
            </div>
            <?php endif;?>
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