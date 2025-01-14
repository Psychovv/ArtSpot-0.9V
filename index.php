<?php

//davigay
//medina = cabicinha burra

require_once 'assets/php/functions.php';
if(isset($_GET['newfp'])){
    unset($_SESSION['auth_temp']);
    unset($_SESSION['forgot_email']);
    unset($_SESSION['forgot_code']);
}
if(isset($_SESSION['Auth'])){
    $user = getUser($_SESSION['userdata']['id']);
    $posts = filtrarPosts();
    $follow_suggestions = filtrarSugestoesDeSeguidores();
}

$pagecount = count($_GET);


//manage pages
if(isset($_SESSION['Auth']) && $user['ac_status']==1 && !$pagecount){
    showPage('header',['page_title'=>'vini']);
    showPage('navbar');
    showPage('wall');
}elseif(isset($_SESSION['Auth']) && $user['ac_status']==0 && !$pagecount){

    showPage('header',['page_title'=>'Verifique seu e-mail']);
    showPage('verify_email');
}elseif(isset($_SESSION['Auth']) && $user['ac_status']==2 && !$pagecount){
    showPage('header',['page_title'=>'Bloqueado']);
    showPage('blocked');
}elseif(isset($_SESSION['Auth']) && isset($_GET['editprofile']) && $user['ac_status']==1){
    showPage('header',['page_title'=>'Editar Perfil']);
    showPage('navbar');
    showPage('edit_profile');
}elseif(isset($_SESSION['Auth']) && isset($_GET['u']) && $user['ac_status']==1){
    $profile = getUsuarioporUsername($_GET['u']);
    if(!$profile){
        showPage('header',['page_title'=>'Usuário não encontrado']);
        showPage('navbar');
        showPage('user_not_found');

    }else{
     $profile_post = getPostagemPorUsuario($profile['id']);  
     $profile['followers']=getSeguidores($profile['id']);
     $profile['following']=getSeguindo($profile['id']);
        showPage('header',['page_title'=>$profile['first_name'].' '.$profile['last_name']]);
        showPage('navbar');
        showPage('profile');
    }
 
  
}elseif(isset($_GET['signup'])){
    showPage('header',['page_title'=>'ArtSpot - SignUp']);
    showPage('signup');
}elseif(isset($_GET['login'])){
   
    showPage('header',['page_title'=>'ArtSpot - Login']);
    showPage('login');
}elseif(isset($_GET['forgotpassword'])){
    
    showPage('header',['page_title'=>'ArtSpot - Esqueceu a senha?']);
    showPage('forgot_password');
}else{
    if(isset($_SESSION['Auth']) && $user['ac_status']==1){
        showPage('header',['page_title'=>'vini']);
        showPage('navbar');
        showPage('wall');
    }elseif(isset($_SESSION['Auth']) && $user['ac_status']==0){

        showPage('header',['page_title'=>'Verifique Seu E-mail']);
        showPage('verify_email');
    }elseif(isset($_SESSION['Auth']) && $user['ac_status']==2){
        showPage('header',['page_title'=>'Blocked']);
        showPage('blocked');
    }else{
        showPage('header',['page_title'=>'ArtSpot - Login']);
        showPage('login');
    }
  
}


showPage('footer');
unset($_SESSION['error']);
unset($_SESSION['formdata']);







