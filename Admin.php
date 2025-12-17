<?php

class Admin{
    private ?string $id;
    private ?string $fullName;
    private ?string $password;
    private ?string $email;

    public function __construct(string $id, string $fullName, string $password, string $email){
        $this->id = $id;
        $this->fullName = $fullName;
        $this->password = $password;
        $this->email = $email;
    }

    public function getID(){ return $this->id;}
    public function getFullName() { return $this->fullName;}
    public function getPassword(){ return $this->password;}
    public function getEmail(){ return $this->email;}

    public function getInput(){   
        echo " ====  WELCOME BACK ==== " . PHP_EOL;
        echo PHP_EOL;

        while(true){
            $email = trim(readline( " | Enter Email: "));
            if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
             echo " | Invalid email. Try again" . PHP_EOL;
            continue;

            }else{
                break;
            }
        }

       while (true) {
            $password = trim(readline(" | Enter Password: "));
            if (strlen($password) < 6) {
             if (strlen($password) < 6) {
                echo " | Password must be at least 6 characters long. " . PHP_EOL;
                }
                continue;
            }
                break;
    }
        return [$email, $password];
    }


    public static function admin_login(){
        $pdo = DatabaseHelper::getPDOInstance();

        list($email, $pass) = (new Admin("", "", "", "", "",""))->getInput();  

        try{
            $stmt = $pdo->prepare(" SELECT * FROM admins WHERE email = :email");
            $stmt->bindparam(':email', $email);
            $stmt->execute();
            $admin = $stmt->fetch();

            $confirm = AppManager::confirm(" | LOGIN ?? : ");
            if($confirm){
            if($admin && password_verify($pass, $admin['password'])){
            echo " | Login successful!" . PHP_EOL . " Welcome " . $admin['email'] . PHP_EOL;
             echo PHP_EOL;

            AppManager::adminDashboard();
            
            }else{
             echo " | Login failed. Invalid email or password." . PHP_EOL;
                Admin::admin_login();

                }
            }

        }catch(PDOException $e){
            echo " | Login Failed:" . $e->getMessage() . PHP_EOL;
        }
    }

}

?>