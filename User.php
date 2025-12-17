<?php

class User{

    public static ?string $userLoggedInId = null;
    private string  $id;
    private string $fullname;
    private string  $password;
    private string $email;
    private string  $reg_no;

    public function __construct(
        string $id,
        string $fullname, 
        string $password,
        string $reg_no,
        string $email
        ){

            $this->id = $id;
            $this->fullname = $fullname;
            $this->password = $password;
            $this->email = $email;
            $this->reg_no = $reg_no;          
    }

    public function getID(){ return $this->id;}
    public function getFullname(){ return $this->fullname;}
    public function getPassword(){ return $this->password;}
    public function getEmail(){ return $this->email;}
    public function getRegNo(){ return $this->reg_no;}


    public static function getUserInput(){
        $id = uniqid();

         while(true){
            $fullName = trim(strtoupper(readline(" | Enter Full Name: ")));
            if(empty($fullName) || strlen($fullName) < 3){
            echo " | Name must not be empty  and less than 3characters" . PHP_EOL;
            continue;

         }else{
                break;
            }

        }

        while(true){
            $email = trim(readline(" | Enter email: "));
            if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                echo " | Invalid email address" . PHP_EOL;
                continue;
            }else{
                break;
            }
        }

        while(true){
            $password = trim(readline(" | Create Password: "));
            $confirmPass = trim(readline(" | Confirm your Password: "));
            
            if($password != $confirmPass){
                echo " Password does not match! Try again" . PHP_EOL;
                continue;

                if(strlen($password) < 6){
                    echo " | Password must be more than 6 character" . PHP_EOL;
                    continue;
                }
            }
                $pass = password_hash($password, PASSWORD_DEFAULT);
                break;
            
        }

        $reg_no =str_pad(random_int(100,999) , 4, "0" ,STR_PAD_LEFT);

        return [$id, $fullName, $pass , $reg_no, $email];
        
    }


    public static function userReg(){
        $pdo = DatabaseHelper::getPDOInstance();

        try{
            list($id, $fullName, $pass, $reg_no, $email) = (new User("", "", "", "", ""))->getUserInput();
            $stmt = $pdo->prepare(" SELECT * FROM users WHERE  email = :email");
            $stmt->bindparam(':email', $email);
            $stmt->execute();

            $row = $stmt->fetch();
            if(!empty($row)){
                echo "Email exists already. Try again" . PHP_EOL;
                User::UserLogin();

            }else{ 

            $stmt = $pdo->prepare( " INSERT INTO users (id, fullName, password, reg_no, email) VALUES (:id, :fullname, :password, :reg_no, :email)");

            $stmt->bindparam(':id', $id);
            $stmt->bindparam(':fullname', $fullName);
            $stmt->bindparam(':password', $pass);
            $stmt->bindparam(':reg_no', $reg_no);
            $stmt->bindparam(':email', $email);
            $stmt->execute();

            $confirm = AppManager::confirm(" | Register ");
            if($confirm){
            echo " | User registration successful!" . PHP_EOL;
            echo " | Your registration number is:" . $reg_no . PHP_EOL;
            echo PHP_EOL;
            AppManager::userDashboard();
                }else{
                    echo " | Registration failed! Try again" . PHP_EOL;
                }
               
            }
        }catch(PDOException $e){
            echo " | Failed Due to an Unknown Error" . $e->getMessage() . PHP_EOL;
        }
        
    }

    public static function UserLogin(){
        $pdo = DatabaseHelper::getPDOInstance();
        echo " ==== WELCOME BACK ===" . PHP_EOL;
        while (true) {
             $reg_no = trim(readline( " | Enter your registration number: "));
             if (! is_numeric($reg_no)) {
                 echo " | Invalid Input" . PHP_EOL;
                 continue;
             }else{
                break;
             }
        }
       
       while (true) {
           $password = trim(readline(" | Enter your password:"));
           if (empty($password) || strlen($password) < 6){
            echo " | Invalid Input OR Less than 6 characters" . PHP_EOL;
            continue;
           }else{
            break;
           }

       }
        

        try{
            $stmt = $pdo->prepare(" SELECT * FROM users WHERE reg_no = :reg_no");
            $stmt->bindparam(':reg_no', $reg_no);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $confirm = AppManager::confirm(" | Login? :");
            if($confirm){
                if($user && password_verify($password, $user['password'])){  
                 self:: $userLoggedInId = $user['id'];
                echo " == Login successful! == " . PHP_EOL;
                echo " | Welcome " . $user['fullname'] . PHP_EOL;
                echo PHP_EOL;

                AppManager::userDashboard();

                }else{
                    echo " Login failed. Invalid Details, Try again" . PHP_EOL;
                    echo PHP_EOL;
                    self::UserLogin();
                }
            }
        }catch(PDOException $e){
            echo " | Login failed:" . $e->getMessage() . PHP_EOL;

        }

            return true;
        }

 }


?>