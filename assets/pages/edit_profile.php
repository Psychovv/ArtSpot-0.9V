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
<?php global $user;?>
    <div class="container col-md-9 col-sm-12 rounded-0 d-flex justify-content-between">
        <div class="col-12 bg-white border rounded p-4 mt-4 shadow-sm">
            <form method="post" action="assets/php/actions.php?updateprofile" enctype="multipart/form-data">
                <div class="d-flex justify-content-center">


                </div>
                <h1 class="h5 mb-3 fw-normal">Editar Perfil</h1>
                <?php
if(isset($_GET['success'])){
    ?>
<p class="text-success">Perfil Atualizado !</p>

<?php
}
                ?>
                <div class="form-floating mt-1 col-md-6 col-sm-12">
                    <img src="assets/recursos/profile/<?=$user['profile_pic']?>" class="img-thumbnail my-3" style="height:150px;width:150px" alt="...">
                    <div class="mb-3">
                        <label for="formFile" class="form-label">Trocar foto de perfil</label>
                        <input class="form-control" type="file" name="profile_pic" id="formFile">
                    </div>
                </div>
                <?=showError('profile_pic')?>

                <div class="d-flex">
                    <div class="form-floating mt-1 col-6 ">
                        <input type="text" name="first_name" value="<?=$user['first_name']?>" class="form-control rounded-0" placeholder="username/email">
                        <label for="floatingInput">Primeiro nome</label>
                    </div>
                    <div class="form-floating mt-1 col-6">
                        <input type="text" name="last_name" value="<?=$user['last_name']?>" class="form-control rounded-0" placeholder="username/email">
                        <label for="floatingInput">Sobrenome</label>
                    </div>
               


                </div>
                <?=showError('first_name')?>
                <?=showError('last_name')?>
                <div class="d-flex gap-3 my-3">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios1"
                            value="option1" <?=$user['gender']==1?'checked':''?> disabled>
                        <label class="form-check-label" for="exampleRadios1">
                            Male
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios3"
                            value="option2" <?=$user['gender']==2?'checked':''?> disabled>
                        <label class="form-check-label" for="exampleRadios3">
                            Female
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios2"
                            value="option2" <?=$user['gender']==0?'checked':''?> disabled>
                        <label class="form-check-label" for="exampleRadios2">
                            Other
                        </label>
                    </div>
                </div>
                <div class="form-floating mt-1">
                    <input type="email" value="<?=$user['email']?>" class="form-control rounded-0" placeholder="email" disabled>
                    <label for="floatingInput">email</label>
                </div>
                <div class="form-floating mt-1">
                    <input type="text"  value="<?=$user['username']?>" name="username" class="form-control rounded-0" placeholder="username/email">
                    <label for="floatingInput">Nome de Usuário</label>
                </div>
                <?=showError('username')?>

                <div class="form-floating mt-1">
                    <input type="password" name="password" class="form-control rounded-0" id="floatingPassword" placeholder="Password">
                    <label for="floatingPassword">Nova Senha</label>
                </div>

                <div class="mt-3 d-flex justify-content-between align-items-center">
                    <button class="btn btn-primary" type="submit">Atualizar Perfil</button>



                </div>

            </form>
        </div>

    </div>

</body>
</html>



