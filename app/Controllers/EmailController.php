<?php
namespace App\Controllers;

use TCPDF;
use App\Models\Main_Model;
use App\Models\Attach_Model;
use App\Config\AppConstant;

class EmailController extends BaseController
{
    public function __construct(){
    
        $this->store = new Main_Model();
        $this->storeAttach = new Attach_Model();       
    }

     public function index()
    {
        
        
    }


    public function sendEmail()
    {
        $status=0;      
        if(isset($_POST['message']))
        {
            $attachmenName=array();
            $url=array();
            $url=$_POST['url'] ?? [];
            $i=0;
            $to=getenv('MAIL_TO');
            $cc=getenv('MAIL_CC');
            $bcc=getenv('MAIL_BCC');
            $userid=$_POST['userid'];
            $subject=$_POST['subject'];
            $message=$_POST['message'];
            $mailMessage="Subject:".$subject."<br> Message :".$message."<br> Name :".$_POST['name']."<br> Mail Id :".$_POST['email'];
            
            if(!empty($url))
                {   
                  foreach ($url as $value) {
                        $mailMessage.="<br>visit URL: ".$value;
                        $attachmenName[$i]=$value;
                        $i++;
                    }
                }    
        
        //Email instance creation;
        $email=\Config\Services::email();
        $email->setFrom(getenv('MAIL_FROM'));
        $email->setTo($to);
        $email->setCC($cc);
        $email->setBCC($bcc);
        $email->setSubject($subject);
        $email->setMessage($mailMessage);
        
        $upload_dir = './uploads'.DIRECTORY_SEPARATOR;
            if(!empty(array_filter($_FILES['attach']['name']))) {
            foreach ($_FILES['attach']['tmp_name'] as $key => $value) {
             
                $file_tmpname = $_FILES['attach']['tmp_name'][$key];
                $file_name = $_FILES['attach']['name'][$key];
                //$file_size = $_FILES['attach']['size'][$key];
                //$file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
                $filepath = $upload_dir.$file_name;
                

                //echo $filepath;
                if(file_exists($filepath)) {
                    $filepath = $upload_dir.time().$file_name;
                       $attachmenName[$i]=time().$file_name;$i++;
                        
                    if( move_uploaded_file($file_tmpname, $filepath)) {
                        echo "";                     }
                    else {                    
                        echo "Error uploading {$file_name} <br />";
                    }
                }
                else {
                     $attachmenName[$i]=$file_name;$i++;
                    if( move_uploaded_file($file_tmpname, $filepath)) {
                        
                        echo "";
                        }
                    else {                    
                        echo "Error uploading {$file_name} <br />";
                    }
                }
              

                $email->attach($filepath);
            }
        }
        if($email->send())
        {
            $status=1; 
            $insertdata=['userid'=>$userid,'name'=>$_POST['name'],'email'=>$_POST['email'],'subject'=>$subject,'message'=>$_POST['message'],'status'=>$status,'cc'=>$cc,'bcc'=>$bcc];
            $this->store->save($insertdata);
            $email_id = $this->store->getInsertID();
        }
        else
        {
            $status=0;
            $data=$email->printDebugger(['headers']);
            $insertdata=['userid'=>$userid,'name'=>$_POST['name'],'email'=>$_POST['email'],'subject'=>$subject,'message'=>$_POST['message'],'status'=>$status,'cc'=>$cc,'bcc'=>$bcc];
            $this->store->save($insertdata);
            $email_id = $this->store->getInsertID();    
        }

        $attachmentCount=count($attachmenName);
        for ($x = 0; $x < $attachmentCount; $x++) { 
                        $image_data=['id_mail'=>$email_id,'file_name'=>$attachmenName[$x],'status'=>$status];
                        $this->storeAttach->save($image_data);
                        }

        }
        
        if($status==1)
         $result = array('statusCode' => '200', 'message' => 'Success: Mail Sent Successfully', 'result' => '');
        else
        $result = array('statusCode' => '5XX', 'message' => 'FAILURE: Mail Not Sent. Please resend', 'result' => '');

        header('Content-Type: application/json');  // <-- header declaration
        echo json_encode($result, true);    // <--- encode
        exit();
        
    }
    public function fetchMail($userid)
    {
        if(!empty($userid))
        {
            $data=$this->store->where('userid',$userid)->findAll();
            //$data=$this->store->findAll();
            
            if(!empty($data))
                $result = array('statusCode' => '200', 'message' => 'Success: Mail Sent Successfully', 'result' => $data);
            else
                $result = array('statusCode' => '200', 'message' => 'No data found', 'result' => '');
            header('Content-Type: application/json');  // <-- header declaration
            echo json_encode($result, true);    // <--- encode
            exit();
        }
        else
        {
            $result = array('statusCode' => '200', 'message' => 'Enter valid input', 'result' => '');
            header('Content-Type: application/json');  // <-- header declaration
            echo json_encode($result, true);    // <--- encode
            exit();
        }
    }
    public function printpdf()
    {
        $pdf = new mypdf();
        $pdf->setPrintFooter(false);
        $pdf->AddPage();
         $pdf->Image("./assets/img/gems_logo_withtext.png", 11, 9, 40, 27.5);
        
        // Title
        $pdf->SetFont('BebasNeueBold','',29);
        $pdf->SetXY(60, 18);
        $pdf->SetTextColor(5, 106, 162);
        $pdf->Cell(0, 15, 'GOSPEL ECHOING MISSIONARY SOCIETY (GEMS)', 0, false, 'C', 0, '', 0, false, 'M', 'M');

        $pdf->SetXY(60, 23);
        $pdf->SetFont('MonotypeCoversia','',11);
        $pdf->SetTextColor(230, 0,0);
        $pdf->Cell(140,5,'Transforming Peoples to Transform Nations',0,1,'C');
        $pdf->Ln();

        $pdf->SetXY(60, 30);
        $pdf->SetFont('ArialBold','B',11);
        $pdf->SetTextColor(0);
        $pdf->Cell(140,10,'GEMS, Sikaria, Indrapuri PO, Dehri On Sone, Rohtas Dist. Bihar 821308',0,1,'C');
        $pdf->Ln();

        $pdf->SetFont('Arial','',11);
        $pdf->SetXY(60, 36);
        $pdf->Cell(140,10,'+916184 234567 - gems@gemsbihar.org | sponsors@gemsbihar.org',0,1,'C');

        $pdf->Ln();

        $pdf->SetFont('Times','I',12);
        $pdf->SetXY(10, 33);
        $pdf->Cell(45,5,'D. Augustine Jebakumar ',0,1,'L');
        $pdf->SetFont('Times','B',12);
        $pdf->Cell(45,5,'General Secretary ',0,1,'C');
        $pdf->Ln();

        $pdf->SetFont('freeserif', '', 12, '', true);

        $output='
        
            <table width="100%" cellspacing="2" cellpadding="2">
            <tr>
                <td><b>Ref:</b> FO/6/SRD/New Sponsor/Allotment </td>
                <td style="text-align:right">Tuesday, February 15, 2022</td>
            </tr>
            <br>
            <tr><td><b>To :</b></td></tr>
            <tr><td style="width:10%"></td>
                <td style="width:90%">Mr./Ms. Franklin L<br>No 5/2 Leela Mahal,Justice Ramanujam,Rbi Colony 
                <br>Thiruvanmiyur,Tamil Nadu (Zone)<br>India  &nbsp; &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Email: lfranklin121@gmail.com
                <br>Ph: 600.141121
                </td>
            </tr>
            <tr><td style="width:100%;line-height: 32px;">Dearly beloved in Christ Jesus,</td></tr>
            <tr><td style="width:100%;line-height: 32px;">Greetings from GEMS family!</td></tr>
            <tr><td style="width:100%"><p style="line-height: 25px;" align="justify">Greetings to you in His blessed name! “God loves a cheerful giver … and will enlarge the harvest of
                                        your righteousness. You will be made rich in every way so that you can be generous on every
                                        occasion”. We greatly appreciate your love and concern for God’s work and for especially coming
                                        forward to sponsor one of our missionary <b>Mr.Samuel Kumar Shake of Sonbhadra.</b></p></td></tr>
            <tr><td style="width:100%"><p style="line-height: 25px;" align="justify">In view of the above, we are glad to inform you that <b>Mr.Samuel Kumar Shake</b> is allotted to be
                                        sponsored by you. This missionary is presently based in <b>Church Planting Ministry</b> under <b>Sonbhadra</b> Zone of GEMS. Your reference number for this missionary is <b>289M</b> For future reference,
                                        kindly use this number.</p></td></tr>
            <tr><td style="width:100%"><p style="line-height: 25px;" align="justify"><b>Mr.Samuel Kumar Shake </b>has been advised to send ministerial reports on a regular basis, which will
                                        enable you to pray and praise the Lord for all His works in this part of the land. We request you to
                                        accept our missionary by upholding their ministry through your fervent prayers and sacrificial offering.
                                        Once again I want to thank you for all that you mean for the cause of His Kingdom and pray that the
                                        God of Peace make you complete in every good work to do His will, working in you what is well
                                        pleasing in His sight, through Jesus Christ, to whom be Glory! God bless you.</p></td></tr>
            </table>
            ';


        $pdf->WriteHTML($output, true, 0, true, 0);
        $this->response->setContentType('application/pdf');
        $pdf->Output('example_001.pdf', 'I');
        return ('Success: Mail Sent Successfully');

    }
    
}


class mypdf extends TCPDF {
        
    //Page header
    public function Header() {
       
        $bMargin = $this->getBreakMargin();

        // Get current auto-page-break mode
        $auto_page_break = $this->AutoPageBreak;

        // Disable auto-page-break
        $this->SetAutoPageBreak(false, 0);

        // Define the path to the image that you want to use as watermark.
        $img_file = './assets/img/gems_logo_layered.jpeg';

        // Render the image
        $this->Image($img_file, 0, 50, 223, 280, '', '', '', false, 300, '', false, false, 0);

        // Restore the auto-page-break status
        $this->SetAutoPageBreak($auto_page_break, $bMargin);

        // Set the starting point for the page content
        $this->setPageMark();
        

    }

    // Page footer
    public function Footer() {
      
      $this->SetXY(10,240);
    $this->Cell(100,45,'Yours in His vineyard,',0,1,'L');
    $this->SetXY(120,270);
    $this->MultiCell(80,6,'I tell you the truth, anyone who gives you a cup of water in my name because you belong to Christ will certainly not lose his reward. (Mark 9:41 NIV) ',1,'J');
    $this->SetX(10);
    $this->Image('./assets/img/sign.png', 10,265, 40, 20.5);
    $this->SetXY(10,280);
    $this->Cell(100,15,'D. Augustine Jebakumar,',0,1,'L');
    $this->SetXY(10,266);
    $this->Cell(100,55,'Copy to: Promotional Office ('.$this->size.') ,',0,1,'L');
    ob_end_clean(); }
}
