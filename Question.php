<?php

class Question{
    private ?string $id;
    private ?string $course;
    private ?string $subject;
    private ?string $question;
    private ?array $options;
    private ?int $answer;

    public function __construct(
        ?string $id, 
        ?string $course,
         ?string $subject,
        ?string $question,
        ?array $options,
        ?int $answer
        ){

            $this->id = $id;
            $this->course = $course;
            $this->subject = $subject;
            $this->question = $question;
            $this->options = $options;
            $this->answer = $answer;
    }

    public function getID(){ return $this->id;}
    public function getCourse(){ return $this->course;}
    public function getSubject(){ return $this->subject;}
    public function getQuestion(){ return $this->question;}
    public function getOptions(){ return $this->options;}
    public function getAnswer(){ return $this->answer;}


    public  function getQuestionInput(){
        $id = uniqid();

        while(true){
            $course = trim (readline(" | Enter Course (Art, Science or Commerce): "));
            if(empty($course) || strlen($course) < 3){
                echo " | Field must not be empty and must contain more than 3 character" . PHP_EOL;
                continue;
            }else{
                break;
            }

        }

        while(true){
            $subject = trim(readline(" | Enter subject: "));
            if(empty($subject) ||  strlen($subject) < 3){
                echo " | Please fill this field! and Must contain more than 3 character" . PHP_EOL;
                continue;
            }else{
                break;
            }
        }

        while(true){
            $question = trim(readline(" | Enter question: "));
            if(empty($question) || strlen($question) < 3){
                echo " | Please fill this filled! and  Must be more than 3 character " . PHP_EOL;
                continue;
            }else{
                break;
            }
        }
        $options = [];
        foreach(range(1,4) as $i){
            while(true){
                $option = trim(readline(" | Enter option $i:"));
                if($option == ''){
                    echo " | Please Option must not be empty " . PHP_EOL;
                    continue;
                }
                $options[] = $option;
                break;
            }
        }

        while(true){
            $answer = trim(readline(" | Enter Correct Answer option: "));
            if(empty($answer)){
                echo " | Please enter the correct " . PHP_EOL;
                continue;
            }else{
                break;
            }
        }

        return [$id,$course,$subject,$question,$options, $answer];
    }

    public static function mapToQuestionRow(array $row){
        $id = $row['id'] ?? "";
        $course = $row['course'] ?? "";
        $subject = $row['subject'] ?? "";
        $question = $row['question'] ?? "";
        $options = json_decode($row['options'] ?? "[]");
        $answer = $row['answer'] ?? 0;
        return new Question($id,$course,$subject,$question, $options, $answer);
    }


     public static function addQuestion(){

        echo " ==== ADD QUESTION ====" . PHP_EOL;
        echo PHP_EOL;
        $pdo = DatabaseHelper::getPDOInstance();

        try{
            list($id,$course,$subject,$question, $options, $answer) = (new Question("","","","",[], 0))->getQuestionInput();

            $optionJson = json_encode($options);

            $stmt = $pdo->prepare("INSERT INTO question_bank(id, course, subject, question, options, answer) VALUES(:id, :course, :subject, :question, :options, :answer)");

            $stmt->bindparam(':id', $id);
            $stmt->bindparam(':course', $course);
            $stmt->bindparam(':subject', $subject);
            $stmt->bindparam(':question', $question);
            $stmt->bindparam(':options', $optionJson);
            $stmt->bindparam(':answer', $answer);
            $stmt->execute();

            $confirm = AppManager::confirm(" ADD(yes/no)");
            if($confirm){
            echo " | Question added successfully!!" . PHP_EOL;
                AppManager::adminDashboard();
            }else{
                echo " Failed due to an Unknown error" . PHP_EOL;
            }
        }catch(PDOException $e){
            echo " | Failed Due to an Unknown Error " . $e->getMessage() . PHP_EOL;
        }
    }

    public static function viewQuestions(){
        $pdo = DatabaseHelper::getPDOInstance();

        try{
            $stmt = $pdo->prepare( " SELECT * FROM question_bank");
            $stmt->execute();
            $questions = [];
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach($rows as $row){
                $question = Question::mapToQuestionRow($row);
                $questions[] = $question;

            }
            return $questions;

        }catch(PDOException $e){
            echo " | Failed Due to an Unknown Error" . $e->getMessage() . PHP_EOL;
        }
    }

    public static function questionDetails(){
        echo " ==== VIEW QUESTIONS ====" . PHP_EOL;
        echo PHP_EOL;

        try{
            $questions = Question::viewQuestions();
            echo "------------------------" . PHP_EOL;
            $index = 1;

             foreach($questions as $question){
                $text = $question->getQuestion();
                $subject = $question->getSubject();
                echo " $index :  $subject |  ". "  $text" . PHP_EOL;
                $index++;
             }

            $option = trim(readline(" | Enter an option:"));
            if(is_numeric($option) && $option > 0 && $option <= count($questions)){
                    $selectedQuestion = $questions[$option - 1];
                    echo " | Course: " . $selectedQuestion->getCourse() . PHP_EOL;
                    echo " | Subject: " . $selectedQuestion->getSubject() . PHP_EOL;
                    echo " | Question: " . $selectedQuestion->getQuestion() . PHP_EOL;
                    $options = $selectedQuestion->getOptions();
                    foreach($options as $index => $option){
                        echo " | Option " . ($index + 1) . ": " . $option . PHP_EOL;
                    }
                    echo " | Correct Answer Option: " . $selectedQuestion->getAnswer() . PHP_EOL;

                echo PHP_EOL;
                echo " | E. Edit Question" . PHP_EOL;
                echo " | D. Delete Question" . PHP_EOL;
                echo " | 0. <- Go Back" . PHP_EOL;  
                echo "------------------------------" . PHP_EOL;
                $choice = readline(" | Enter option:");  

            switch(strtolower($choice)){
                case 'e':
                    Question::updateQuestion($selectedQuestion);
                    break;
                case 'd':
                    Question::deleteQuestion($selectedQuestion);
                    break; 
                case 0:
                    AppManager::adminDashboard();
                    break;
                    default: 
                        echo " | Invalid option" . PHP_EOL;
                        Question::questionDetails();
                        break;      
                } 
            }
        }catch(PDOException $e){
            echo " | Failed Due to an Unknown Error" . $e->getMessage() . PHP_EOL;
        }
                                   
    }

    public static function getValidInput($prompt, $previousValue, $minLength = 1){
        while (true) {
            $input = trim(readline($prompt));
            if(empty($input)){
                return $previousValue;
            }elseif (strlen($input) < $minLength) {
                echo "  |  Must be more than $minLength characters" . PHP_EOL;
            }else{
                return $input;
            }
        }
    }

    public static function getUpdateInput($currentData){
            $newCourse = self::getValidInput(" | Enter Course: ", $currentData->getCourse());
            $newSubject = self::getValidInput(" | Enter subject:", $currentData->getSubject());
            $newQuestion = self::getValidInput(" | Enter question:", $currentData->getQuestion());
            $newOptions = [];
            foreach(range(1,4) as $i){
                $newOption = self::getValidInput(" | Enter option $i:", $currentData->getOptions());
                $newOptions[] = $newOption;
            }
            
            $newAnswer = self::getValidInput(" | Enter Correct Answer option:", $currentData->getAnswer());
                
            return [
               'course' => $newCourse,
                'subject' => $newSubject, 
                'question' => $newQuestion, 
                'options' => $newOptions, 
                'answer' => $newAnswer 
            ];

    }


    public static function updateQuestion ($selectedQuestion){
        $pdo = DatabaseHelper::getPDOInstance();
        echo " ==== UPDATE QUESTIONS ====" . PHP_EOL;
        echo PHP_EOL;
        try{
            $newData = Question::getUpdateInput($selectedQuestion);
            $id = $selectedQuestion->getID();
            $options = json_encode($newData['options']);

            $query = " UPDATE question_bank SET course = :course, subject = :subject, question = :question, options = :options, answer = :answer WHERE id = :id ";
           
                $confirm = AppManager::confirm(" SAVE(yes/no): ");
                if($confirm){

                $stmt = $pdo->prepare($query);
                $stmt->bindparam(':id', $id);
                $stmt->bindparam(':course', $newData['course']);
                $stmt->bindparam(':subject', $newData['subject']);
                $stmt->bindparam(':question', $newData['question']);
                $stmt->bindparam(':options' , $options);
                $stmt->bindparam(':answer', $newData['answer'] );
                $stmt->execute();

                echo " | Question updated successfully" . PHP_EOL;

                }

        }catch(PDOException $e){
           echo  " | Failed Due to an Unknown Error" . $e->getMessage() . PHP_EOL;
        }


    } 

    public static function deleteQuestion($selectedQuestion){
        $pdo = DatabaseHelper::getPDOInstance();
        try {
    
            $confirm = AppManager::confirm( " | Do you want to delete this question? : ") .  PHP_EOL;
            $id = $selectedQuestion->getId();
            $query = " DELETE FROM question_bank WHERE id = :id;";
            if($confirm){
                $stmt = $pdo->prepare($query);
                $stmt->bindparam(':id', $id);
                $stmt->execute();

                echo "  | Question deleted successfully". PHP_EOL;
                Admin::adminDashboard();
                if($stmt->rowCount()  == 0){
                    echo " | No Question Found". PHP_EOL;
                }

            }else{
                echo " | Deletion Cancelled" . PHP_EOL;
                AppManager::adminDashboard();
            }


        }catch(PDOException $e){
            echo " | Failed Due to an Unknown Error" . PHP_EOL;
        }
    }

    public static function deleteAllQuestions(){
        $pdo = DatabaseHelper::getPDOInstance();

        try{
            $confirm = AppManager::confirm(" | Do you want to delete the questions(y/n)?:");
            $confirm1 = AppManager::confirm(" | Are you sure you want to delete(y/n)?");
            if($confirm && $confirm1 == true){
                $stmt = $pdo->prepare(" DELETE FROM question_bank");
                $stmt->execute();

                if($stmt->rowCount() > 0){
                    echo " | Delete successfully" . PHP_EOL;
                }else{
                    echo  " | No Question Found"  . PHP_EOL;
                }
            }else{
                echo " | Deletion Cancelled! ". PHP_EOL;
                echo PHP_EOL;
                return;
                AppManager::adminDashboard();
            }

        }catch(PDOException $e){
            echo " | Contact Deletion Failed " . PHP_EOL;
            echo PHP_EOL;
        }

    }

 }

 ?>
