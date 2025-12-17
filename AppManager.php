<?php

    require_once 'DatabaseHelper.php' ;
    require_once 'Admin.php';
    require_once 'User.php' ;
    require_once 'Question.php';
    require_once 'Quiz.php';
    
class AppManager{
    
      public static function confirm($prompt = "Continue? (y/n): ") {
        while (true) {
            $ans = strtolower(readLine($prompt));
            if (in_array($ans, ['y', 'yes'], true)) return true;
            if (in_array($ans, ['n', 'no'], true)) return false;
            echo "Please answer y or n.\n";
        }
        while (true) {
            $ans2 = strtolower(readLine(""));
            if (in_array($ans, ['y', 'yes'], true)) return true;
            if (in_array($ans, ['n', 'no'], true)) return false;
            echo "Please answer y or n.\n";
        }

    }


    public static function menu(){
        echo " ==== WELCOME TO MY QUIZ APP ====" . PHP_EOL; 
        echo PHP_EOL;
        echo "  ==================   " . PHP_EOL;
        echo " | 1. Admin Login  " . PHP_EOL;
        echo " | 2. User Registration   " . PHP_EOL;
        echo " | 3. Exit " . PHP_EOL;
        echo " ==================== " . PHP_EOL;

        $choice = readline(" | Please select an option:")  . PHP_EOL;

        switch($choice){
            case 1:
               Admin::admin_login();
                break;
            case 2:
                AppManager::userHandler();
                break;
            case 3:
                default:
                echo " ==== App Closed!!!! ====" . PHP_EOL;
                exit;

        }

    }
    
    public static function userHandler(){

        echo " ==== WELCOME TO USER MENU ==== " . PHP_EOL;
        echo PHP_EOL;

        echo "  ----------------------------------  " . PHP_EOL;
        $confirm = AppManager::confirm(" | Do you have an account? (y/n): ");
        if($confirm){
            " | User Login" . PHP_EOL;
            User::UserLogin();

        }else{
            " | User Registration " . PHP_EOL;
            User::userReg();    
        }
        echo "  ----------------------------------  " . PHP_EOL;
    }

    public static function adminDashboard(){
        echo " ====  WELCOME TO ADMIN DASHBOARD ====" . PHP_EOL;

        echo PHP_EOL;
        echo "------------------------------" . PHP_EOL;
        echo " | 1. Add Questions" . PHP_EOL;
        echo " | 2. View Questions" . PHP_EOL;
        echo " | 3. Delete Questions" . PHP_EOL;
        echo " | 0 <- Go Back" . PHP_EOL;
        echo "------------------------------" . PHP_EOL;

        $option = readline(" | Enter an Option: ");
        switch($option){
            case 1:
                Question::addQuestion();
                break;
            case 2:
                Question::questionDetails();
                break;
            case 3:
                Question::deleteAllQuestions();
                break;
            case 0;
                AppManager::menu();
                break;

        }
    }



    public static function userDashboard(){
        echo " ===== WELCOME BACK!!!! =====" . PHP_EOL;
        echo PHP_EOL;
        echo " ---------------------------------- " . PHP_EOL;
        echo " | 1. Take Quiz " . PHP_EOL;
        echo " | 2.  View Result" . PHP_EOL;
        echo " | 3. Log Out" . PHP_EOL;
        echo " | 0 <- Go Back" . PHP_EOL;
        echo " ---------------------------------- " . PHP_EOL;
        echo PHP_EOL;

        $option = readline(" | Enter an option:");
        switch($option){
            case 1:
                 Quiz::quizLogic(User::$userLoggedInId);
                break;
            case 2:
                Quiz::viewResult(User::$userLoggedInId);
                break;
            case 3:
                echo " | Log Out successful" . PHP_EOL;
                Exit;
                break;
            case 0:
                AppManager::menu();
                break;
        }

    }
}

?>
