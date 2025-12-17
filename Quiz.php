<?php

class Quiz{
    private string $id;
    private string $subject;
    private array $questions;
    private array $options;
    private int $answer;
    private string $userId;
    private int  $timeLimit;
    private int $score;

    public function  __construct(
        string $id,
        string $course,
        string $subject,
        array $questions,
        array $options,
        int $answer,
        string $userId,
        int $timeLimit,
        int $score = 0

    ){
        $this->id = $id;
        $this->subject = $subject;
        $this->questions = $questions;
        $this->options= $options;
        $this->answer= $answer;
        $this->userId = $userId;
        $this->timeLimit = $timeLimit;
        $this->score = $score;
    }

    public function getID(){ return $this->id;}
    public function getSubject(){ return $this->subject;}
    public function getQuestions(){ return $this->questions;}
    public function getOptions(){ return $this->options;}
    public function getAnswer(){ return $this->answer;}
    public function getUserId(){ return $this->userId;}
    public function getTimeLimit(){ return $this->timeLimit;}
    public  function getScore(){ return $this->score;}


   public static function Timer($startTime, $timeLimit, $timeAlert){
      
        try{
            $endTime =  $startTime + ($timeLimit * 60);

            $timeLeft = $endTime - time();
            if( $timeLeft <= 10 *60 && !$timeAlert){
                echo " | You have  10 Minutes left" . PHP_EOL;
                 $timeAlert = true;             
            }

             if(time() >= $endTime ){

                echo " | Time is Up " . PHP_EOL;
                   return false; 
            }
            return true;
        }catch(PDOException $e){
            echo " | Failed Due to sn Unknown Error" . $e->getMessage() . PHP_EOL;
        }
    }


    public static function Instruction(){

        echo " ===== Welcome !! =====" . PHP_EOL;
        echo PHP_EOL;

        echo " ==== READ INSTRUCTION CAREFULLY ====" . PHP_EOL;
        echo PHP_EOL;

        echo " | Read each question carefully, choose the best answer, 
        select only one answer per question, manage your time wisely, 
        and review your answers before submitting the quiz. 
        When you are ready to start, Type READY"  . PHP_EOL;

        while (true) {
             $input = strtolower(readline( " Ready(yes/no): "));
            if($input  == 'yes'  ){
                echo " Quiz started" . PHP_EOL;
                break;
            }elseif($input  == 'no' ){
                echo " | Restarting Instruction" . PHP_EOL;
                sleep(1);
                Quiz::Instruction();
            }else{
                echo " | Please enter a valid input (yes/no)" . PHP_EOL;
            }
        }
       
    }


        public static function quizQuestions(){
            $pdo = DatabaseHelper::getPDOInstance();
            while (true) {
               $subject = (readline(" | Enter subject: "));
                if(strlen($subject) <  3){
                    echo " | Must  be more than 3 characters" . PHP_EOL;
                    continue;
                }else{
                    break;
                }
            }

            try{

                $query = " SELECT * FROM  question_bank WHERE lower(subject) = :subject;";

                $stmt = $pdo->prepare($query);
                $stmt->bindparam(':subject', $subject);
                $stmt->execute();

                $questions = [];

                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($rows as $row) {
                    $question = Question::mapToQuestionRow($row);
                    $questions[] = $question;
                }

                    shuffle($questions);

                    return [
                        "subject" => $subject,
                        "question" => $questions
                    ];

            }catch(PDOException $e){
                echo " Error previewing question" .  $e->getMessage() . PHP_EOL;
            }

        }

        public static function  quizLogic(?string $userId){

            try{

                self::Instruction();
                $data = self::quizQuestions();
                $userId = User::$userLoggedInId; 
                $subject = $data['subject'];
                $questions = $data['question'];
                $startTime = time();
                $timeLimit = 10;
                $timeAlert = false;
                
                $score = 0;
                $qindex = 0;

                if ($userId == null) {
                $userId = User::$userLoggedInId;
                }

                while ($qindex < count($questions)) {

                  if (!self::Timer($startTime, $timeLimit, $timeAlert)) {
                        echo " | Time expired!" . PHP_EOL;
                        break;
                    }
               
                $question = $questions[$qindex];
                echo "Question " . ($qindex + 1) . ": " . $question->getQuestion() . PHP_EOL;
                $options = $question->getOptions();
                $oindex = 1;
                foreach ($options as $option) {
                    echo "-Option $oindex: $option" . PHP_EOL;
                    $oindex++;
                }

                $answer = trim(readline("Enter answer (or 'next', 'back'): "));

                if (strtolower($answer) == 'next') {
                    $qindex++;
                    continue;

                } elseif (strtolower($answer) == 'back') {
                    $qindex--;
                    continue;

                }elseif(strtolower($answer) == 'submit'){
                    echo " Submitted successfully" . PHP_EOL;
                    break;

                }

                    if ($answer == $question->getAnswer()) {
                             $score++;
                        }

                        $qindex++;
                    }

                    echo " =======================================" . PHP_EOL;


                        $submit = readline(" | SUBMIT (yes/no) : ");
                        if(strtolower($submit) == 'yes'){
                             if($userId !== null){
                                self::saveQuizResult($userId, $subject, $score);
                                return $score;

                            }

                        }elseif (strtolower($submit) == 'no') {
                            Quiz::quizLogic(User::$userLoggedInId);
                        }
                    

                    echo " Your score: $score out of " . count($questions) . PHP_EOL;

                    if($userId !== null){
                    self::saveQUizResult($userId, $subject, $score);
                    AppManager::userDashboard();

                    }

                    return $score;
            }catch(PDOException $e){
                echo " | Failed Due to an Unknown Error " . $e->getMessage() . PHP_EOL;
            }

         }


            public static function saveQuizResult(string $userId, string $subject, int $score ){

                $pdo = DatabaseHelper::getPDOInstance();

                $id = uniqid();
                $timeStamp = (new DateTimeImmutable())->format("Y-m-d H:i:s");

                $query = " INSERT INTO quiz_results(id, user_id, subject, score, quiz_taken_at) VALUES (:id, :user_id, :subject, :score, :quiz_taken_at)";
                try{
                    $stmt = $pdo->prepare($query);
                    $stmt->bindparam(':id', $id);
                    $stmt->bindparam(':user_id', $userId);
                    $stmt->bindparam(':subject', $subject);
                    $stmt->bindparam(':score', $score);
                    $stmt->bindparam(':quiz_taken_at', $timeStamp);
                    $stmt->execute();

                    echo " Result saved successfully" . PHP_EOL;
                    AppManager::userDashboard();

                }catch(PDOException $e){
                    echo " Result not saved" . $e->getMessage() . PHP_EOL;
                }

            }

            public static function viewResult(string $userId){

                $pdo = DatabaseHelper::getPDOInstance();
                $index = 1;

                $query = " SELECT * FROM quiz_results WHERE user_id = :userId ORDER BY score DESC";

                try{
                    $stmt = $pdo->prepare($query);
                    $stmt->bindparam(':userId', $userId);
                    $stmt->execute();

                    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if (empty($results)) {

                    echo "No quiz results found." . PHP_EOL;
                    return;

                    }

                    echo " ==== QUIZ RESULTS ====" . PHP_EOL;
                    foreach ($results as  $result) {
                        echo " | Subject: " . $result['subject'] . PHP_EOL;
                        echo " | Score: " . $result['score'] . PHP_EOL;
                        echo " | Take At: " . $result['quiz_taken_at'] . PHP_EOL;
                        echo "  ==================================" . PHP_EOL;
                        $index ++;

                    }
                    
                }catch(PDOException $e){
                    echo " Failed Due to an Unknown Error" . $e->getMessage() . PHP_EOL;
                }

                echo " | 0 <- Go Back " . PHP_EOL;
                $option = readline(" | Enter option: ") ;
                if($option == 0){
                    AppManager::userDashboard();
                }
            }

        }
