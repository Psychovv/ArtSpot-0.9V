<?php
require_once 'config.php';
$db = mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME) or die("database is not connected");

//function for showing pages
function showPage($page,$data=""){
include("assets/pages/$page.php");
}



//for getting ids of chat users
function getIdChatUsuario(){
    global $db;
    $current_user_id = $_SESSION['userdata']['id'];
    $query = "SELECT from_user_id,to_user_id FROM messages WHERE to_user_id=$current_user_id || from_user_id=$current_user_id ORDER BY id DESC";
    $run = mysqli_query($db,$query);
    $data =  mysqli_fetch_all($run,true);
    $ids=array();
    foreach($data as $ch){
    if($ch['from_user_id']!=$current_user_id && !in_array($ch['from_user_id'],$ids)){
       $ids[]=$ch['from_user_id'];
    }

    if($ch['to_user_id']!=$current_user_id && !in_array($ch['to_user_id'],$ids)){
        $ids[]=$ch['to_user_id'];
     }

    }

    return $ids;
}

function getMensagens($user_id){
    global $db;
    $current_user_id = $_SESSION['userdata']['id'];
    $query = "SELECT * FROM messages WHERE (to_user_id=$current_user_id && from_user_id=$user_id) || (from_user_id=$current_user_id && to_user_id=$user_id) ORDER BY id DESC";
    $run = mysqli_query($db,$query);
    return  mysqli_fetch_all($run,true);
}

function enviarMensagem($user_id,$msg){
    global $db;
    $current_user_id = $_SESSION['userdata']['id'];
    $query = "INSERT INTO messages (from_user_id,to_user_id,msg) VALUES($current_user_id,$user_id,'$msg')";
    return mysqli_query($db,$query);

}

function contadorDeMensagem(){
global $db;
$current_user_id = $_SESSION['userdata']['id'];
$query="SELECT COUNT(*) as row FROM messages WHERE to_user_id=$current_user_id && read_status=0";
$run=mysqli_query($db,$query);
return mysqli_fetch_assoc($run)['row'];
}

function attStatusVisualizado($user_id){
    $cu_user_id = $_SESSION['userdata']['id'];
    global $db;
    $query="UPDATE messages SET read_status=1 WHERE to_user_id=$cu_user_id && from_user_id=$user_id";
    return mysqli_query($db,$query);
}

function gettime($date){
    return date('H:i - (F jS, Y )', strtotime($date));
}

function getTodasMsg(){
    $active_chat_ids = getIdChatUsuario();
    $conversation=array();
    foreach($active_chat_ids as $index=>$id){
        $conversation[$index]['user_id'] = $id;
        $conversation[$index]['messages'] = getMensagens($id);
    }
    return $conversation;
}

//function for follow the user
function seguirUsuario($user_id){
    global $db;
    $cu = getUser($_SESSION['userdata']['id']);
    $current_user=$_SESSION['userdata']['id'];
    $query="INSERT INTO follow_list(follower_id,user_id) VALUES($current_user,$user_id)";
  
    criarNotificacao($cu['id'],$user_id,"Começou a te seguir !");
    return mysqli_query($db,$query);
    
}



//function for blocking the user
function bloquearUsuario($blocked_user_id){
    global $db;
    $cu = getUser($_SESSION['userdata']['id']);
    $current_user=$_SESSION['userdata']['id'];
    $query="INSERT INTO block_list(user_id,blocked_user_id) VALUES($current_user,$blocked_user_id)";
  
    criarNotificacao($cu['id'],$blocked_user_id,"blocked you");
    $query2="DELETE FROM follow_list WHERE follower_id=$current_user && user_id=$blocked_user_id";
    mysqli_query($db,$query2);
    $query3="DELETE FROM follow_list WHERE follower_id=$blocked_user_id && user_id=$current_user";
    mysqli_query($db,$query3);

   
    return mysqli_query($db,$query);
    
}

//for unblocking the user
function desbloquearUsuario($user_id){
    global $db;
    $current_user=$_SESSION['userdata']['id'];
    $query="DELETE FROM block_list WHERE user_id=$current_user && blocked_user_id=$user_id";
    criarNotificacao($current_user,$user_id,"Unblocked you !");
    return mysqli_query($db,$query);   
}

//function checarLike
function checarLike($post_id){
    global $db;
    $current_user = $_SESSION['userdata']['id'];
    $query="SELECT count(*) as row FROM likes WHERE user_id=$current_user && post_id=$post_id";
    $run = mysqli_query($db,$query);
    return mysqli_fetch_assoc($run)['row'];
}

//function for like the post
function like($post_id){
    global $db;
    $current_user=$_SESSION['userdata']['id'];
    $query="INSERT INTO likes(post_id,user_id) VALUES($post_id,$current_user)";
   $poster_id = getPostagemId($post_id);
   
   if($poster_id!=$current_user){
    criarNotificacao($current_user,$poster_id,"Curtiu sua postagem !",$post_id);
   }
   

    return mysqli_query($db,$query);
    
}




//function for creating comments
function adicionarComentario($post_id,$comment){
    global $db;
 $comment = mysqli_real_escape_string($db,$comment);

    $current_user=$_SESSION['userdata']['id'];
    $query="INSERT INTO comments(user_id,post_id,comment) VALUES($current_user,$post_id,'$comment')";
    $poster_id = getPostagemId($post_id);

    if($poster_id!=$current_user){
        criarNotificacao($current_user,$poster_id,"Comentou no seu post",$post_id);
    }
   

    return mysqli_query($db,$query);
    
}


//function for creating comments
function criarNotificacao($from_user_id,$to_user_id,$msg,$post_id=0){
    global $db;
    $query="INSERT INTO notifications(from_user_id,to_user_id,message,post_id) VALUES($from_user_id,$to_user_id,'$msg',$post_id)";
    mysqli_query($db,$query);    
}



//function for getting likes count
function getComentarios($post_id){
    global $db;
    $query="SELECT * FROM comments WHERE post_id=$post_id ORDER BY id DESC";
    $run = mysqli_query($db,$query);
    return mysqli_fetch_all($run,true);
}

//get notifications

function getNotificacoes(){
  $cu_user_id = $_SESSION['userdata']['id'];

    global $db;
    $query="SELECT * FROM notifications WHERE to_user_id=$cu_user_id ORDER BY id DESC";
    $run = mysqli_query($db,$query);
    return mysqli_fetch_all($run,true);
}



function getMensagensNaoLidas(){
    $cu_user_id = $_SESSION['userdata']['id'];
  
      global $db;
      $query="SELECT count(*) as row FROM notifications WHERE to_user_id=$cu_user_id && read_status=0 ORDER BY id DESC";
      $run = mysqli_query($db,$query);
      return mysqli_fetch_assoc($run)['row'];
  }

  function show_time($time){
    return '<time style="font-size:small" class="timeago text-muted text-small" datetime="'.$time.'"></time>';
  }

  function setNotificacaoLida(){
       $cu_user_id = $_SESSION['userdata']['id'];
      global $db;
      $query="UPDATE notifications SET read_status=1 WHERE to_user_id=$cu_user_id";
      return mysqli_query($db,$query);
  }



//function for getting likes count
function getLikes($post_id){
    global $db;
    $query="SELECT * FROM likes WHERE post_id=$post_id";
    $run = mysqli_query($db,$query);
    return mysqli_fetch_all($run,true);
}

//function for unlike the post
function unlike($post_id){
    global $db;
    $current_user=$_SESSION['userdata']['id'];
    $query="DELETE FROM likes WHERE user_id=$current_user && post_id=$post_id";
    
    $poster_id = getPostagemID($post_id);
    if($poster_id!=$current_user){
        criarNotificacao($current_user,$poster_id,"Removeu a curtida da sua postagem !",$post_id);
    }
  
    return mysqli_query($db,$query);
}
function deixarDeSeguir($user_id){
    global $db;
    $current_user=$_SESSION['userdata']['id'];
    $query="DELETE FROM follow_list WHERE follower_id=$current_user && user_id=$user_id";

    criarNotificacao($current_user,$user_id,"Deixou de te seguir !");
    return mysqli_query($db,$query);
 
    
}


//function for show errors
function showError($field){
if(isset($_SESSION['error'])){
    $error =$_SESSION['error'];
    if(isset($error['field']) && $field==$error['field']){
       ?>
<div class="alert alert-danger my-2" role="alert">
  <?=$error['msg']?>
</div>
       <?php
    }
}
}


//function for show prevformdata
function showFormData($field){
    if(isset($_SESSION['formdata'])){
        $formdata =$_SESSION['formdata'];
        return $formdata[$field];
    }
 
}


//for checking duplicate email
function emailRegistrado($email){
    global $db;
    $query="SELECT count(*) as row FROM users WHERE email='$email'";
    $run=mysqli_query($db,$query);
    $return_data = mysqli_fetch_assoc($run);
    return $return_data['row'];
}

//for checking duplicate username
function usernameJaRegistrado($username){
    global $db;
    $query="SELECT count(*) as row FROM users WHERE username='$username'";
    $run=mysqli_query($db,$query);
    $return_data = mysqli_fetch_assoc($run);
    return $return_data['row'];
}

//for checking duplicate username by other
function usernameJaRegistradoPorOutro($username){
    global $db;
    $user_id=$_SESSION['userdata']['id'];
    $query="SELECT count(*) as row FROM users WHERE username='$username' && id!=$user_id";
    $run=mysqli_query($db,$query);
    $return_data = mysqli_fetch_assoc($run);
    return $return_data['row'];
}

//for validating the signup form
function validarCadastro($form_data){
$response=array();
$response['status']=true;
  
    if(!$form_data['password']){
        $response['msg']="senha nao foi inserida";
        $response['status']=false;
        $response['field']='password';
    }
   
    if(!$form_data['username']){
        $response['msg']="nome de usuário não inserido";
        $response['status']=false;
        $response['field']='username';
    }
    
    if(!$form_data['email']){
        $response['msg']="email não foi inserido";
        $response['status']=false;
        $response['field']='email';
    }
    
    if(!$form_data['last_name']){
        $response['msg']="sobrenome não foi inserido";
        $response['status']=false;
        $response['field']='last_name';
    }
    if(!$form_data['first_name']){
        $response['msg']="Primeiro nome não foi inserido";
        $response['status']=false;
        $response['field']='first_name';
    }
    if(emailRegistrado($form_data['email'])){
        $response['msg']="email já registrado";
        $response['status']=false;
        $response['field']='email';
    }
    if(usernameJaRegistrado($form_data['username'])){
        $response['msg']="nome de usuário já registrado";
        $response['status']=false;
        $response['field']='username';
    }

    return $response;

}


//for validate the login form
function validarFormularioLogin($form_data){
    $response=array();
    $response['status']=true;
    $blank=false;
      
        if(!$form_data['password']){
            $response['msg']="senha não inserida";
            $response['status']=false;
            $response['field']='password';
            $blank=true;
        }
       
        if(!$form_data['username_email']){
            $response['msg']="nome de usuário/email não inseridos";
            $response['status']=false;
            $response['field']='username_email';
            $blank=true;
        }

        if(!$blank && !checarUsuario($form_data)['status'] ){
            $response['msg']="algo está errado, não achamos você";
            $response['status']=false;
            $response['field']='checarUsuario';
        }else{
            $response['user']=checarUsuario($form_data)['user'];
        }
        
      
       
       
    
    
        return $response;
    
    }


//for checking the user
function checarUsuario($login_data){
    global $db;
 $username_email = $login_data['username_email'];
 $password=md5($login_data['password']);

 $query = "SELECT * FROM users WHERE (email='$username_email' || username='$username_email') && password='$password'";
 $run = mysqli_query($db,$query);
 $data['user'] = mysqli_fetch_assoc($run)??array();
 if(count($data['user'])>0){
     $data['status']=true;
 }else{
    $data['status']=false;

 }

 return $data;
}


//for getting userdata by id
function getUser($user_id){
    global $db;
 $query = "SELECT * FROM users WHERE id=$user_id";
 $run = mysqli_query($db,$query);
 return mysqli_fetch_assoc($run);

}


//for filtering the suggestion list
function filtrarSugestoesDeSeguidores(){
$list = getSugestoesDeSeguidoress();
$filter_list  = array();
foreach($list as $user){
    if(!checarStatusSeguindo($user['id']) && !checkBS($user['id']) && count($filter_list)<5){
     $filter_list[]=$user;
    }
}

return $filter_list;
}

//for checking the user is followed by current user or not
function checarStatusSeguindo($user_id){
    global $db;
    $current_user = $_SESSION['userdata']['id'];
    $query="SELECT count(*) as row FROM follow_list WHERE follower_id=$current_user && user_id=$user_id";
    $run = mysqli_query($db,$query);
    return mysqli_fetch_assoc($run)['row'];
}

//for checking the user is followed by current user or not
function checkBlockStatus($current_user,$user_id){
    global $db;
    
    $query="SELECT count(*) as row FROM block_list WHERE user_id=$current_user && blocked_user_id=$user_id";
    $run = mysqli_query($db,$query);
    return mysqli_fetch_assoc($run)['row'];
}


function checkBS($user_id){
    global $db;
    $current_user = $_SESSION['userdata']['id'];
    $query="SELECT count(*) as row FROM block_list WHERE (user_id=$current_user && blocked_user_id=$user_id) || (user_id=$user_id && blocked_user_id=$current_user)";
    $run = mysqli_query($db,$query);
    return mysqli_fetch_assoc($run)['row'];
}
//

//for getting users for follow suggestions
function getSugestoesDeSeguidoress(){
    global $db;

    $current_user = $_SESSION['userdata']['id'];
    $query = "SELECT * FROM users WHERE id!=$current_user";
    $run = mysqli_query($db,$query);
    return mysqli_fetch_all($run,true);
}

//get followers count
function getSeguidores($user_id){
    global $db;
    $query = "SELECT * FROM follow_list WHERE user_id=$user_id";
    $run = mysqli_query($db,$query);
    return mysqli_fetch_all($run,true);
}

//get followers count
function getSeguindo($user_id){
    global $db;
    $query = "SELECT * FROM follow_list WHERE follower_id=$user_id";
    $run = mysqli_query($db,$query);
    return mysqli_fetch_all($run,true);
}

//for getting posts by id
function getPostagemPorUsuario($user_id){
    global $db;
 $query = "SELECT * FROM posts WHERE user_id=$user_id ORDER BY id DESC";
 $run = mysqli_query($db,$query);
 return mysqli_fetch_all($run,true);

}

//for getting post
function getPostagemId($post_id){
    global $db;
 $query = "SELECT user_id FROM posts WHERE id=$post_id";
 $run = mysqli_query($db,$query);
 return mysqli_fetch_assoc($run)['user_id'];

}

//for searching the users
function procurarUsuario($keyword){
    global $db;
 $query = "SELECT * FROM users WHERE username LIKE '%".$keyword."%' || (first_name LIKE '%".$keyword."%' || last_name LIKE '%".$keyword."%') LIMIT 5";
 $run = mysqli_query($db,$query);
 return mysqli_fetch_all($run,true);

}




//for getting userdata by username
function getUsuarioporUsername($username){
    global $db;
 $query = "SELECT * FROM users WHERE username='$username'";
 $run = mysqli_query($db,$query);
 return mysqli_fetch_assoc($run);



}

//for getting posts
function getPost(){
    global $db;
 $query = "SELECT users.id as uid,posts.id,posts.user_id,posts.post_img,posts.post_text,posts.post_music,posts.created_at,users.first_name,users.last_name,users.username,users.profile_pic FROM posts JOIN users ON users.id=posts.user_id ORDER BY id DESC";

 $run = mysqli_query($db,$query);
 return mysqli_fetch_all($run,true);

}


function deletarPost($post_id){
    global $db;
$user_id=$_SESSION['userdata']['id'];
    $dellike = "DELETE FROM likes WHERE post_id=$post_id && user_id=$user_id";
    mysqli_query($db,$dellike);
    $delcom = "DELETE FROM comments WHERE post_id=$post_id && user_id=$user_id";
    mysqli_query($db,$delcom);
    $not = "UPDATE notifications SET read_status=2 WHERE post_id=$post_id && to_user_id=$user_id";
mysqli_query($db,$not);


    $query = "DELETE FROM posts WHERE id=$post_id";
    return mysqli_query($db,$query);
}

//for getting posts dynamically
function filtrarPosts(){
    $list = getPost();
    $filter_list  = array();
    foreach($list as $post){
        if(checarStatusSeguindo($post['user_id']) || $post['user_id']==$_SESSION['userdata']['id']){
         $filter_list[]=$post;
        }
    }
    
    return $filter_list;
    }



//for creating new user
function createUser($data){
 global $db;
 $first_name = mysqli_real_escape_string($db,$data['first_name']);
 $last_name = mysqli_real_escape_string($db,$data['last_name']);
 $gender = $data['gender'];
 $email = mysqli_real_escape_string($db,$data['email']);
 $username = mysqli_real_escape_string($db,$data['username']);
 $password = mysqli_real_escape_string($db,$data['password']);
 $password = md5($password);

 $query = "INSERT INTO users(first_name,last_name,gender,email,username,password) ";
 $query.="VALUES ('$first_name','$last_name',$gender,'$email','$username','$password')"; 
 return mysqli_query($db,$query);
}

//function for verify email
function verifyEmail($email){
    global $db;
    $query="UPDATE users SET ac_status=1 WHERE email='$email'";
    return mysqli_query($db,$query);
}

//function for verify email
function resetPassword($email,$password){
    global $db;
    $password=md5($password);
    $query="UPDATE users SET password='$password' WHERE email='$email'";
    return mysqli_query($db,$query);
}

//for validating update form
function validateUpdateForm($form_data,$image_data){
    $response=array();
    $response['status']=true;
      

        if(!$form_data['username']){
            $response['msg']="nome de usuário nao inserido";
            $response['status']=false;
            $response['field']='username';
        }
        
        if(!$form_data['last_name']){
            $response['msg']="sobrenome não inserido";
            $response['status']=false;
            $response['field']='last_name';
        }
        if(!$form_data['first_name']){
            $response['msg']="primeiro nome não inserido";
            $response['status']=false;
            $response['field']='first_name';
        }
  
        if(usernameJaRegistradoPorOutro($form_data['username'])){
            $response['msg']=$form_data['username']." já esta registrado";
            $response['status']=false;
            $response['field']='username';
        }
    
       if($image_data['name']){
           $image = basename($image_data['name']);
           $type = strtolower(pathinfo($image,PATHINFO_EXTENSION));
           $size = $image_data['size']/1000;

           if($type!='jpg' && $type!='jpeg' && $type!='png'){
            $response['msg']="apenas jpg,jpeg,png são permitidas";
            $response['status']=false;
            $response['field']='profile_pic';
        }

        if($size>1000){
            $response['msg']="a imagem teve ter menos de 1mb";
            $response['status']=false;
            $response['field']='profile_pic';
        }
       }

        return $response;
    
    }
    

    //function for updating profile

    function updateProfile($data,$imagedata){
        global $db;
        $first_name = mysqli_real_escape_string($db,$data['first_name']);
        $last_name = mysqli_real_escape_string($db,$data['last_name']);
        $username = mysqli_real_escape_string($db,$data['username']);
        $password = mysqli_real_escape_string($db,$data['password']);

if(!$data['password']){
$password = $_SESSION['userdata']['password'];
}else{
    $password = md5($password);
    $_SESSION['userdata']['password']=$password;
}

$profile_pic="";
if($imagedata['name']){
    $image_name = time().basename($imagedata['name']);
    $image_dir="../recursos/profile/$image_name";
    move_uploaded_file($imagedata['tmp_name'],$image_dir);
    $profile_pic=", profile_pic='$image_name'";
}
       
      
    
        $query = "UPDATE users SET first_name = '$first_name', last_name='$last_name',username='$username',password='$password' $profile_pic WHERE id=".$_SESSION['userdata']['id'];
return mysqli_query($db,$query);

    }


    //for validating add post form
function validatePostImage($image_data){
    $response=array();
    $response['status']=true;
      

        if(!$image_data['name']){
            $response['msg']="imagem não selecionada";
            $response['status']=false;
            $response['field']='post_img';
        }
        
   
    
       if($image_data['name']){
           $image = basename($image_data['name']);
           $type = strtolower(pathinfo($image,PATHINFO_EXTENSION));
           $size = $image_data['size']/1000;

           if($type!='jpg' && $type!='jpeg' && $type!='png' && $type!='mp3'){
            $response['msg']="apenas mp3 são permitidos";
            $response['status']=false;
            $response['field']='post_img';
        }

        if($size>20000){
            $response['msg']="musica deve ter menos de 20mb";
            $response['status']=false;
            $response['field']='post_img';
        }
       }

        return $response;
    
    }

    function validatePostMusic($music_data) {
        $response = array();
        $response['status'] = true;
    
        if (!isset($music_data['name']) || empty($music_data['name'])) {
            $response['msg'] = "música não selecionada";
            $response['status'] = false;
            $response['field'] = 'post_music';
        } else {
            $image = basename($music_data['name']);
            $type = strtolower(pathinfo($image, PATHINFO_EXTENSION));
            $size = $music_data['size'] / 1000; // Assuming the size is in bytes
    
            if ($type !== 'mp3') {
                $response['msg'] = "apenas arquivos MP3 são permitidos";
                $response['status'] = false;
                $response['field'] = 'post_music';
            }
    
            if ($size > 20000) {
                $response['msg'] = "a música deve ter menos de 20 KB";
                $response['status'] = false;
                $response['field'] = 'post_music';
            }
        }
    
        return $response;
    }

    //for creating new user
function criarPost($text,$image,$music){
    global $db;
    $post_text = mysqli_real_escape_string($db,$text['post_text']);
$user_id = $_SESSION['userdata']['id'];

        $image_name = time().basename($image['name']);
        $image_dir="../recursos/posts/$image_name";
        move_uploaded_file($image['tmp_name'],$image_dir);

        $music_name = time().basename($music['name']);
        $music_dir="../recursos/posts/$music_name";
        move_uploaded_file($music['tmp_name'],$music_dir);
    

    $query = "INSERT INTO posts(user_id,post_text,post_img, post_music)";
    $query.="VALUES ($user_id,'$post_text','$image_name', '$music_name')"; 
    return mysqli_query($db,$query);
   }

   // for getting posts

   
?>