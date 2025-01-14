<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        body {
            background-color: #101728;
            color: white;
            margin: 0; /* Remova a margem padrão para ocupar toda a largura */
        }

        .container {
            display: flex; /* Use flexbox para alinhar os elementos internos */
            justify-content: space-between; /* Alinhe os elementos com espaço entre eles */
            padding: 10px; /* Adicione algum espaço interno ao container */
        }

        .col-md-8 {
            flex: 1; /* Faça a primeira coluna (onde os posts estão) crescer para ocupar o espaço disponível */
            max-width: calc(80% - 20px); /* Defina uma largura máxima para os posts */
            overflow: auto; /* Adicione uma barra de rolagem caso haja muitos posts */
        }

        .col-lg-4 {
            flex: 0 0 20%; /* Defina uma largura fixa para a segunda coluna (barra lateral) */
            max-width: 20%;
            overflow: auto; /* Adicione uma barra de rolagem caso haja muitos itens na barra lateral */
        }

        /* Estilos existentes... */
        .background {
            background-color: black;
        }

        audio {
            width: 267px;
            height: 30px;
            border-radius: 0;
        }




    </style>
</head>

<body>




    <?php
    global $user;
    global $posts;
    global $follow_suggestions;

    ?>
    <div class="container col-md-12 col-sm-12 col-lg-12 rounded-0 d-flex justify-content-between">
        <div class="col-md-8 col-sm-12" style="max-width: 80%; margin-left: 10%;">
        <div class="d-flex flex-wrap "> 

            <?php
            foreach ($posts as $post) {
                $likes = getLikes($post['id']);
                $comments = getComentarios($post['id']);
            ?>
            
                <div class="card mt-4 background" Style="width: calc(33.33% - 30px); margin-right: 20px; padding left:">
                    <div class="card-title d-flex justify-content-right  align-items-center background">

                        <div class="d-flex align-items-center p-2">
                            <img src="assets/recursos/profile/<?= $post['profile_pic'] ?>" alt="" height="30" width="30" class="rounded-circle border">&nbsp;&nbsp;<a href='?u=<?= $post['username'] ?>' class="text-decoration-none text-white"><?= $post['first_name'] ?> <?= $post['last_name'] ?></a>
                        </div>
                        <div class="p-2">
                            <?php
                            if ($post['uid'] == $user['id']) {
                            ?>

                                <div class="dropdown">

                                    <i class="bi bi-three-dots-vertical" id="option<?= $post['id'] ?>" data-bs-toggle="dropdown" aria-expanded="false"></i>

                                    <ul class="dropdown-menu" aria-labelledby="option<?= $post['id'] ?>">
                                        <li><a class="dropdown-item" href="assets/php/actions.php?deletarPost=<?= $post['id'] ?>"><i class="bi bi-trash-fill"></i> Deletar Post</a></li>
                                    </ul>
                                </div>
                            <?php
                            }
                            ?>

                        </div>
                    </div>

                    <img src="assets/recursos/posts/<?= $post['post_img'] ?>" loading=lazy class="" alt="...">
                    <audio src="assets/recursos/posts/<?= $post['post_music'] ?>" controls loading=lazy class="" alt="..."> </audio>
                    <h4 style="font-size: x-larger" class="p-2 border-bottom d-flex">
                        <span>
                            <?php
                            if (checarLike($post['id'])) {
                                $like_btn_display = 'none';
                                $unlike_btn_display = '';
                            } else {
                                $like_btn_display = '';
                                $unlike_btn_display = 'none';
                            }
                            ?>
                            <i class="bi bi-heart-fill unlike_btn text-danger" style="display:<?= $unlike_btn_display ?>" data-post-id='<?= $post['id'] ?>'></i>
                            <i class="bi bi-heart like_btn" style="display:<?= $like_btn_display ?>" data-post-id='<?= $post['id'] ?>'></i>

                        </span>
                        <i class=" d-flex align-items-center"><span class="p-1 mx-2 text-small" style="font-size:small" data-bs-toggle="modal" data-bs-target="#postview<?= $post['id'] ?>"><?= count($likes) ?> likes</span></i>

                    </h4>
                    
                    <?php
                    if ($post['post_text']) {
                    ?>
                        <div style="text-align:center; max: widith 70px;" class="card-body">
                            <?= $post['post_text'] ?>
                        </div>
                    <?php
                    }
                    ?>


                </div>
                <div class="modal fade" id="postview<?= $post['id'] ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl modal-dialog-centered">
                        <div class="modal-content">

                            <div class="modal-body d-md-flex p-0">
                                <div class="col-md-8 col-sm-12">
                                    <img src="assets/recursos/posts/<?= $post['post_img'] ?>" style="max-height:90vh" class="w-100 overflow:hidden">
                                </div>



                                <div class="col-md-4 col-sm-12 d-flex flex-column">
                                    <div class="d-flex align-items-center p-2 border-bottom">
                                        <div><img src="assets/recursos/profile/<?= $post['profile_pic'] ?>" alt="" height="50" width="50" class="rounded-circle border">
                                        </div>
                                        <div>&nbsp;&nbsp;&nbsp;</div>
                                        <div class="d-flex flex-column justify-content-start">
                                            <h6 style="margin: 0px;"><?= $post['first_name'] ?> <?= $post['last_name'] ?></h6>
                                            <p style="margin:0px;" class="text-muted">@<?= $post['username'] ?></p>
                                        </div>
                                        <div class="d-flex flex-column align-items-end flex-fill">
                                            <div class=""></div>
                                            <div class="dropdown">
                                                <span class="<?= count($likes) < 1 ? 'disabled' : '' ?>" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <?= count($likes) ?> likes
                                                </span>
                                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                                    <?php
                                                    foreach ($likes as $like) {
                                                        $lu = getUser($like['user_id']);
                                                    ?>
                                                        <li><a class="dropdown-item" href="?u=<?= $lu['username'] ?>"><?= $lu['first_name'] . ' ' . $lu['last_name'] ?> (@<?= $lu['username'] ?>)</a></li>

                                                    <?php
                                                    }
                                                    ?>

                                                </ul>
                                            </div>
                                            <div style="font-size:small" class="text-muted">Posted <?= show_time($post['created_at']) ?> </div>

                                        </div>
                                    </div>
                                </div>



                            </div>

                        </div>
                    </div>
                </div>

                <div class="modal fade" id="likes<?= $post['id'] ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Likes</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <div class="modal-body">
                                <?php
                                if (count($likes) < 1) {
                                ?>
                                    <p>Currently No Likes</p>
                                <?php
                                }
                                foreach ($likes as $f) {

                                    $fuser = getUser($f['user_id']);
                                    $fbtn = '';
                                    if (checkBS($f['user_id'])) {
                                        continue;
                                    } else if (checarStatusSeguindo($f['user_id'])) {
                                        $fbtn = '<button class="btn btn-sm btn-danger unfollowbtn" data-user-id=' . $fuser['id'] . ' >Unfollow</button>';
                                    } else if ($user['id'] == $f['user_id']) {
                                        $fbtn = '';
                                    } else {
                                        $fbtn = '<button class="btn btn-sm btn-primary followbtn" data-user-id=' . $fuser['id'] . ' >Follow</button>';
                                    }
                                ?>
                                    <div class="d-flex justify-content-between">
                                        <div class="d-flex align-items-center p-2">
                                            <div><img src="assets/recursos/profile/<?= $fuser['profile_pic'] ?>" alt="" height="40" width="40" class="rounded-circle border">
                                            </div>
                                            <div>&nbsp;&nbsp;</div>
                                            <div class="d-flex flex-column justify-content-center">
                                                <a href='?u=<?= $fuser['username'] ?>' class="text-decoration-none text-dark">
                                                    <h6 style="margin: 0px;font-size: small;"><?= $fuser['first_name'] ?> <?= $fuser['last_name'] ?></h6>
                                                </a>
                                                <p style="margin:0px;font-size:small" class="text-muted">@<?= $fuser['username'] ?></p>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <?= $fbtn ?>

                                        </div>
                                    </div>
                                <?php
                                }
                                ?>
                            </div>

                        </div>
                    </div>
                </div>

            <?php
            }
            ?>
        </div>
  </div>
    </div>
    

</body>

</html>