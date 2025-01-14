<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        body{
            background-color:#101728;
        }
    </style>
</head>
<body>
    
<div class="login">
        <div class="col-sm-12 col-md-4 bg-white border rounded p-4 shadow-sm">
            <form method="post" action="assets/php/actions.php?login">
                <div class="d-flex justify-content-center">
  
                    <H5>ArtSpot</H5>
                </div>
                <h1 class="h5 mb-3 fw-normal">Entrar</h1>

                <div class="form-floating">
                    <input type="text" name="username_email" value="<?=showFormData('username_email')?>" class="form-control rounded-0" placeholder="username/email">
                    <label for="floatingInput">Nome de Usuario/e-mail</label>
                </div>
                <?=showError('username_email')?>
                <div class="form-floating mt-1">
                    <input type="password" name="password" class="form-control rounded-0" id="floatingPassword" placeholder="Password">
                    <label for="floatingPassword">Senha</label>
                </div>
                <?=showError('password')?>
                <?=showError('checkuser')?>


                <div class="mt-3 d-flex justify-content-between align-items-center">
                    <button class="btn btn-primary" type="submit">Entrar</button>
                    <a href="?signup" class="text-decoration-none">Criar nova conta</a>


                </div>
            </form>
        </div>
    </div>


</body>
</html>




