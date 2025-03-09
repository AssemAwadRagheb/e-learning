<div class="app-main__outer">
    <div class="app-main__inner">

        <?php 
            @$exam_id = $_GET['exam_id'];
            $filter = isset($_GET['filter']) ? $_GET['filter'] : '';

            if($exam_id != "") {
                $selEx = $conn->query("SELECT * FROM exam_tbl WHERE ex_id='$exam_id' ")->fetch(PDO::FETCH_ASSOC);
                $exam_course = $selEx['cou_id'];
                $selExmne = $conn->query("SELECT * FROM examinee_tbl et WHERE exmne_course='$exam_course' ");
                ?>
                <div class="app-page-title">
                    <div class="page-title-wrapper">
                        <div class="page-title-heading">
                            <div><b class="text-primary">RANKING BY EXAM</b><br>
                                Exam Name : <?php echo $selEx['ex_title']; ?><br><br>
                                <span class="border" style="padding:10px;color:black;background-color: yellow;">Excellence</span>
                                <span class="border" style="padding:10px;color:white;background-color: green;">Very Good</span>
                                <span class="border" style="padding:10px;color:white;background-color: blue;">Good</span>
                                <span class="border" style="padding:10px;color:white;background-color: red;">Failed</span>
                                <span class="border" style="padding:10px;color:black;background-color: #E9ECEE;">Not Answering</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <form method="GET" action="">
                    <input type="hidden" name="page" value="ranking-exam">
                    <input type="hidden" name="exam_id" value="<?php echo $exam_id; ?>">
                    <label for="filter">Filter by Grade:</label>
                    <select name="filter" id="filter">
                        <option value="">All</option>
                        <option value="A" <?php if ($filter == 'A') echo 'selected'; ?>>A</option>
                        <option value="B" <?php if ($filter == 'B') echo 'selected'; ?>>B</option>
                        <option value="C" <?php if ($filter == 'C') echo 'selected'; ?>>C</option>
                        <option value="F" <?php if ($filter == 'F') echo 'selected'; ?>>F</option>
                        <option value="NA" <?php if ($filter == 'NA') echo 'selected'; ?>>Not Answered</option>
                    </select>
                    <input type="submit" value="Filter">
                </form>
                
                <div class="table-responsive">
                    <table class="align-middle mb-0 table table-borderless table-striped table-hover" id="tableList">
                        <thead>
                            <tr>
                                <th width="25%">Examinee Fullname</th>
                                <th>Score / Over</th>
                                <th>Percentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            while ($selExmneRow = $selExmne->fetch(PDO::FETCH_ASSOC)) { 
                                $exmneId = $selExmneRow['exmne_id'];
                                $selScore = $conn->query("SELECT * FROM exam_question_tbl eqt INNER JOIN exam_answers ea ON eqt.eqt_id = ea.quest_id AND eqt.exam_answer = ea.exans_answer WHERE ea.axmne_id='$exmneId' AND ea.exam_id='$exam_id' AND ea.exans_status='new' ORDER BY ea.exans_id DESC");
                                $selAttempt = $conn->query("SELECT * FROM exam_attempt WHERE exmne_id='$exmneId' AND exam_id='$exam_id' ");

                                $over = $selEx['ex_questlimit_display'];
                                $score = $selScore->rowCount();
                                $ans = $score / $over * 100;

                                // Determine grade category
                                $category = '';
                                if ($selAttempt->rowCount() == 0) {
                                    $category = 'NA';
                                } else if ($ans >= 90) {
                                    $category = 'A';
                                } else if ($ans >= 80) {
                                    $category = 'B';
                                } else if ($ans >= 75) {
                                    $category = 'C';
                                } else {
                                    $category = 'F';
                                }

                                // Filter by category if filter is set
                                if ($filter && $filter != $category) continue;

                                ?>
                                <tr style="<?php 
                                    if ($category == 'NA') {
                                        echo "background-color: #E9ECEE;color:black";
                                    } else if ($category == 'A') {
                                        echo "background-color: yellow;";
                                    } else if ($category == 'B') {
                                        echo "background-color: green;color:white";
                                    } else if ($category == 'C') {
                                        echo "background-color: blue;color:white";
                                    } else {
                                        echo "background-color: red;color:white";
                                    }
                                    ?>">
                                    <td><?php echo $selExmneRow['exmne_fullname']; ?></td>
                                    <td>
                                        <?php 
                                        if ($category == 'NA') {
                                            echo "Not answered yet";
                                        } else {
                                            echo $score . " / " . $over;
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php 
                                        if ($category == 'NA') {
                                            echo "Not answered yet";
                                        } else {
                                            echo number_format($ans, 2) . "%";
                                        }
                                        ?>
                                    </td>
                                </tr>
                            <?php } ?>                              
                        </tbody>
                    </table>
                </div>
                <?php
            } else { ?>
                <div class="app-page-title">
                    <div class="page-title-wrapper">
                        <div class="page-title-heading">
                            <div><b>RANKING BY EXAM</b></div>
                        </div>
                    </div>
                </div> 

                <div class="col-md-12">
                    <div class="main-card mb-3 card">
                        <div class="card-header">Exam List</div>
                        <div class="table-responsive">
                            <table class="align-middle mb-0 table table-borderless table-striped table-hover" id="tableList">
                                <thead>
                                    <tr>
                                        <th class="text-left pl-4">Exam Title</th>
                                        <th class="text-left">Course</th>
                                        <th class="text-left">Description</th>
                                        <th class="text-center" width="8%">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $selExam = $conn->query("SELECT * FROM exam_tbl ORDER BY ex_id DESC");
                                    if ($selExam->rowCount() > 0) {
                                        while ($selExamRow = $selExam->fetch(PDO::FETCH_ASSOC)) { ?>
                                            <tr>
                                                <td class="pl-4"><?php echo $selExamRow['ex_title']; ?></td>
                                                <td>
                                                    <?php 
                                                    $courseId = $selExamRow['cou_id']; 
                                                    $selCourse = $conn->query("SELECT * FROM course_tbl WHERE cou_id='$courseId'");
                                                    while ($selCourseRow = $selCourse->fetch(PDO::FETCH_ASSOC)) {
                                                        echo $selCourseRow['cou_name'];
                                                    }
                                                    ?>
                                                </td>
                                                <td><?php echo $selExamRow['ex_description']; ?></td>
                                                <td class="text-center">
                                                    <a href="?page=ranking-exam&exam_id=<?php echo $selExamRow['ex_id']; ?>" class="btn btn-success btn-sm">View</a>
                                                </td>
                                            </tr>
                                        <?php }
                                    } else { ?>
                                        <tr>
                                            <td colspan="5">
                                                <h3 class="p-3">No Exam Found</h3>
                                            </td>
                                        </tr>
                                    <?php }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>   
            <?php } ?>      
    </div>
</div>
