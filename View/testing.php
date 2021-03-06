<link rel="stylesheet" href="css/Thanh-Style-testing.css"/>

<?php
if (isset($_GET['id'])) $maDe = $_GET['id'];
if (isset($_GET['pageNum'])) $pageNum = $_GET['pageNum'];
settype($pageNum, 'int');
if ($pageNum < 0) $pageNum = 1;
$url = str_replace("/ToeicThi/", "", $_SERVER['REQUEST_URI']);
// css cho textarea
if (!isset($_SESSION['login_id']))
    echo '<script>
    $(document).ready(function () {
        $("textarea[name=comment]").css("color","red");
        $("textarea[name=comment]").css("text-align","center");
        $("textarea[name=comment]").css("padding-top","35px");
        $("textarea[name=comment]").css("font-size","20px");
    });
</script>'; // end css cho textarea

$kq = $toeic->lay_DeThi_TheoMaDe($maDe);
$row = $kq->fetch_assoc();
$date = date_parse($row['NgayHetHan']);
$NgayThi = $date['day'];
$ThangThi = $date['month'];
$NamThi = $date['year'];
$GioThi = $date['hour'];
$PhutThi = $date['minute'];
$GiayThi = $date['second'];
$NgayThi = mktime($GioThi, $PhutThi, $GiayThi, $ThangThi, $NgayThi, $NamThi);
$thi = date("d/m/Y H:i:s", $NgayThi);
$dateDiff = $NgayThi - time();
$deadline = floor($dateDiff / (60 * 60 * 24));


if (isset($_POST['comment'])) {
    $bl = $_POST['comment'];
    $idUser = $_SESSION['login_id'];
    $toeic->luu_BinhLuan($idUser, $maDe, $bl);
}

if (isset($_POST['submit'])) {
    $dateDiff = $NgayThi - time();
    $deadline = floor($dateDiff / (60 * 60 * 24));
    if (!isset($_SESSION['login_id']))
        echo '<script>alert("Bạn chưa đăng nhập")</script>';
    else {
        if ($deadline >= 0) {
            header("location: ../TOEIC-$maDe/Toeic-Register.html");
        } else {
            echo '<script>alert("Đã quá thời hạn để đăng kí, bạn vui lòng chọn đề thi khác")</script>';
        }
    }
}

if (isset($_POST['test'])) {
    $dateDiff = $NgayThi - time();
    $deadline = floor($dateDiff / (60 * 60 * 24));

    $dateDiff = abs($dateDiff);
    $years = floor($dateDiff / (365 * 60 * 60 * 24));
    $months = floor(($dateDiff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
    $days = floor(($dateDiff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));
    $hours = floor(($dateDiff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24) / (60 * 60));
    $minutes = floor(($dateDiff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24 - $hours * 60 * 60) / 60);

    if (!isset($_SESSION['login_id']))
        echo '<script>alert("Bạn chưa đăng nhập")</script>';
    else {
        $check = $toeic->check_Toeic_Register($_SESSION['login_id'], $maDe);
        if (!$check) {
            if ($deadline < 0) {
                if ($minutes > 5 || $deadline < -1) {
                    echo '<script>alert("Bạn dự thi quá trễ, xin vui lòng chọn lịch thi khác")</script>';
                } else {
                    header("location: Captcha.html");
                }
            } else {
                if ($years == 0) {
                    if ($months == 0) {
                        if ($days == 0) {
                            if ($hours == 0) {
                                echo '<script>alert("Còn ' . $minutes . ' phút cho đến lúc thi");</script>';
                            } else {
                                echo '<script>alert("Còn ' . $hours . ' giờ, ' . $minutes . ' phút cho đến lúc thi");</script>';
                            }
                        } else {
                            echo '<script>alert("Còn ' . $days . ' ngày, ' . $hours . ' giờ, ' . $minutes . ' phút cho đến lúc thi");</script>';
                        }
                    } else {
                        echo '<script>alert("Còn ' . $months . ' tháng, ' . $days . ' ngày, ' . $hours . ' giờ, ' . $minutes . ' phút cho đến lúc thi");</script>';
                    }
                } else
                    echo '<script>alert("Còn ' . $years . ' năm, ' . $months . ' tháng, ' . $days . ' ngày, ' . $hours . ' giờ, ' . $minutes . ' phút cho đến lúc thi");</script>';
            }
        } else {
            echo '<script>alert("Bạn chưa đăng kí thi đề này ")</script>';
        }
    }
}
?>

<style>
    #container #captcha{
        width: 100%;
        height:1000px;
        position: absolute;
        z-index: 1024;
        background-color: white;
        color: black;
        border: 2px solid red;
    }
</style>


<div id="container">
    <div id="main-contain" class="col-md-8">
        <p>&nbsp;<a href="#">Trang chủ</a> / <a href="View/Exam"> Lịch thi Toeic</a> / <a
                    href="View/Exam/TOEIC-<?= $maDe ?>/1">
                Toeic-<?= $maDe ?></a></p>
        <!-- thi thu toeic -->
        <div id="test">
            <div id="heading">
                <p>Đề thi thử Toeic <?= $maDe ?></p>
            </div>
            <p style="padding: 0px 10px;">Chào mừng các bạn đến với đề thi thử TOEIC trong chương trình Luyện thi TOEIC
                online của Desus! Đây là
                bài
                thi mô phỏng dạng đề thi TOEIC thực tế do đội ngũ giáo viên của Desus kì công biên soạn. Bài làm của các
                bạn
                sẽ được chấm điểm và thông báo kết quả ngay sau khi các bạn nộp bài.</p>

            <p id="describe">
                <?php
                echo $row['MoTa'] . " - Số câu hỏi: " . $row['SoCau'] . " câu - Thời lượng: " . $row['ThoiLuong'] . " phút - Ngày thi: " . $thi . " - Lượt đăng kí: " . $row['LuotDangKi'];
                ?>
            </p>
            <?php
            if ($deadline >= 0)
                echo '<p style="color:#ee4b53;text-align: center">Bạn hãy click vào nút đăng kí bên dưới để đặt lịch làm bài. Chúc
                các bạn đạt điểm số thật cao!</p>';
            else
                echo '<p style="color:#ee4b53;text-align: center">Đề đã được phát ra, bạn vui lòng đăng kí sớm hơn thời gian hiện tại</p>';
            ?>

            <form style="text-align: center;" method="post" id="prepare-testing">

                <!--                <a href="View/index.php?p=begin-test"><img src="img/green-start-button.png" width="150" height="150"></a>-->

                <button type="submit" name="submit"><img src="img/register-button.png" width="250" height="150">
                </button>
                <button type="submit" name="test" class="btn"
                        style="width: 250px;height: 115px;margin-left:100px;color:#ff6200">Làm bài thi
                </button>
            </form>

            <br>
        </div>          <!-- end thi thu toeic -->

        <!-- comment va danh muc -->
        <div id="feedback">
            <!-- binh luan -->
            <div id="comment" class="col-md-10">
                <p><h5>CÁC Ý KIẾN BÌNH LUẬN - PHẢN HỒI VỀ BÀI THI NÀY</h5></p>
                <div id="client-comment">
                    <?php
                    $kqbl = $toeic->lay_binhluan($maDe, $pageNum, 3, $totalRows);
                    ?>
                    <table border="1">
                        <?php while ($rowbl = $kqbl->fetch_assoc()) { ?>
                            <?php
                            $kq = $toeic->lay_UserTheoId($rowbl['NguoiDang']);
                            $row = $kq->fetch_assoc();
                            ?>
                            <tr>
                                <td style="width: 15%;"><img src="img/logo.dethi.jpeg"></td>
                                <td style="width:25%"><strong>Nick name:</strong> <?= $row['Ho'] . " " . $row['Ten'] ?>
                                    <BR><strong>Trình độ Toeic:</strong> ..<BR><strong>Ngày
                                        đăng:</strong> <?= $rowbl['NgayDang'] ?>
                                </td>
                                <td style="width: 60%"><?= $rowbl['NoiDung'] ?></td>
                            </tr>
                        <?php } ?>
                        <tr>
                            <?php
                            $kq = $toeic->lay_DeThi_TheoMaDe($maDe);
                            $rowdt = $kq->fetch_assoc();
                            $deThi = str_replace(" ", "-", $rowdt['TieuDe']);
                            $baseURL = "View/Exam/" . $deThi;
                            ?>
                            <td colspan="3"><?= $toeic->phan_Trang($baseURL, $pageNum, 3, $totalRows) ?></td>
                        </tr>
                    </table>

                    <div id="user-comment">
                        <strong>Ý KIẾN - BÌNH LUẬN CỦA BẠN</strong><br>
                        <form method="post" action="">
                            <textarea
                                    name="comment"<?= (isset($_SESSION['login_id'])) ? "" : "disabled" ?>><?= (isset($_SESSION['login_id'])) ? "" : "BẠN CHƯA ĐĂNG NHẬP" ?></textarea>
                            <input type="submit"
                                   value="Gửi bình luận" <?= (isset($_SESSION['login_id'])) ? "" : "disabled" ?> >
                        </form>
                    </div>
                </div>
            </div> <!-- end binh luan -->

            <!-- danh muc -->
            <div id="category" class="col-md-2">
                <p><h5>DANH MỤC</h5></p>
                <ul style="list-style-type: none">
                    <li id="tag">Các đề thi thử khác</li>
                    <li><a href="#">Đề thi thử Toeic 1</a></li>
                    <li><a href="#">Đề thi thử Toeic 2</a></li>
                    <li><a href="#">Đề thi thử Toeic 3</a></li>
                    <li><a href="#">Đề thi thử Toeic 4</a></li>
                </ul>
                <ul style="list-style-type: none">
                    <li id="tag">Các bài giảng khác</li>
                    <li><a href="#">Luyện nghe</a></li>
                    <li><a href="#">Luyện nói</a></li>
                    <li><a href="#">Luyện đọc</a></li>
                    <li><a href="#">Luyện viết</a></li>
                </ul>
            </div>  <!-- end danh muc -->
        </div>        <!-- end comment va danh muc -->

        <div style="clear: both"></div>
        <br>
    </div>
</div>
