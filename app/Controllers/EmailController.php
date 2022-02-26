<?php
namespace App\Controllers;

use TCPDF;
use App\Models\Main_Model;
use App\Models\Attach_Model;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailController extends BaseController
{
     private $db;

    
    public function __construct(){
        $this->db = db_connect(); // Loading database
        // OR $this->db = \Config\Database::connect();
        $this->store = new Main_Model();
        $this->storeAttach = new Attach_Model();       
    }

     public function index()
    {
        
        
    }


    public function sendEmail()
    {
       // if (strtolower($this->reqMethod) == 'post') {
        //Getting iput from API
        $data= $this->getDataFromUrl('json');
        $to = isset($data['to']) ? $data['to'] : null;
        $cc=isset($data['cc']) ? $data['cc'] : null;
        $bcc=isset($data['bcc']) ? $data['bcc'] : null;
        $userid=isset($data['userid']) ? $data['userid'] : null;
        $subject=isset($data['subject']) ? $data['subject'] : null;
        $mailMessage=isset($data['body']) ? $data['body'] : null;
        $name=isset($data['name']) ? $data['name'] : null;
        $pdfContent=isset($data['pdfContent']) ? $data['pdfContent'] : null;
        $status=0;   
            $mail = new PHPMailer(true);  
            $mail->isSMTP();  
		    $mail->Host         = 'smtp.gmail.com'; //smtp.google.com
		    $mail->SMTPAuth     = true;     
		    $mail->Username     = 'test.work.mail.revathy@gmail.com';  
		    $mail->Password     = 'jyo@123thi';
			$mail->SMTPSecure   = 'tls';  
			$mail->Port         = 587;  
			$mail->Subject      = $subject;
			$mail->Body         = $mailMessage;
			$mail->setFrom($cc, 'Revathy');
			$mail->addAddress($to);  
		    $mail->addCC($cc, "Revathy");
            $mail->addBCC($bcc, "Stalin");
            $mail->isHTML(true);
            if(!$pdfContent)
            {
            //Dont generate pdf
            }
            else
            {
            $pdf = new mypdf();
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);
            $pdf->AddPage();
            $pdf->writeHTML($pdfContent, true, 0, true, 0);
            $this->response->setContentType('application/pdf');
            $generatedPdf=$pdf->Output('test.pdf', 'S');
            $mail->addStringAttachment($generatedPdf,"Ori.PDF");
            }
        
        /*
            $attachmenName=array();
            $url=array();
            $url=$_POST['url'] ?? [];
            $i=0;
            
            if(!empty($url))
                {   
                  foreach ($url as $value) {
                        $mailMessage.="<br>visit URL: ".$value;
                        $attachmenName[$i]=$value;
                        $i++;
                    }
                }    
        */
        
        /*
        $upload_dir = './uploads'.DIRECTORY_SEPARATOR;
            if(!empty(array_filter($_FILES['attach']['name']))) {
            foreach ($_FILES['attach']['tmp_name'] as $key => $value) {
             
                $file_tmpname = $_FILES['attach']['tmp_name'][$key];
                $file_name = $_FILES['attach']['name'][$key];
                $filepath = $upload_dir.$file_name;
                
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
        }*/
        
        if($mail->send())
        {
            $status=1;
        }
        else
        {
            $status=0;    
        }
        $insertdata=['userid'=>$userid,'name'=>$name,'email'=>$to,'subject'=>$subject,'message'=>$mailMessage,'status'=>$status,'cc'=>$cc,'bcc'=>$bcc];
        $this->store->save($insertdata);
        /*
        $email_id = $this->store->getInsertID();
        $attachmentCount=count($attachmenName);
        for ($x = 0; $x < $attachmentCount; $x++) { 
                        $image_data=['id_mail'=>$email_id,'file_name'=>$attachmenName[$x],'status'=>$status];
                        $this->storeAttach->save($image_data);
                        }

        }
        */
        if($status==1)
         return $this->message(200,'Success: Mail Sent Successfully','');
        else
        return $this->message(400,'Server Error','');
        
        /*}
        else
        {
            return $this->message(400, null, 'Method Not Allowed');
        }*/
    }
    public function fetchMail($userid)
    {
        if(!empty($userid))
        {
        $builder = $this->db->table("emaildata as table1");
        $builder->select('table1.*, table2.file_name as attachment');
        $builder->where('table1.userid='.$userid);
        $builder->join('mailattachment as table2', 'table1.id = table2.id_mail');
        
        $query = $builder->get();
       
        foreach($query->getResult() as $row)
        {
            $row->attachment=base_url().'/uploads/'.$row->attachment;
        }
        if(!empty($query->getResult()))
        {
        $result = array('statusCode' => '200', 'message' => 'Succesfully retrieved Result', 'result' => $query->getResult());
            header('Content-Type: application/json');  // <-- header declaration
            echo json_encode($result, true);    // <--- encode
            exit();
        }
        else{
            $result = array('statusCode' => '200', 'message' => 'No mail sent for the given id', 'result' => '');
            header('Content-Type: application/json');  // <-- header declaration
            echo json_encode($result, true);    // <--- encode
            exit();
        }
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
       $data= $this->getDataFromUrl('json');
        $pdfContent=isset($data['pdfContent']) ? $data['pdfContent'] : null;
        //$pdfContent=isset($pdfContent1)?$pdfContent1:"PdfCalling";
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


        $pdf->WriteHTML($pdfContent, true, 0, true, 0);
        $this->response->setContentType('application/pdf');
        $pdf->Output('test.pdf', 'I');
       

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
        $img_file = './assets/img/watermark.png ';

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
