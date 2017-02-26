<?
    class image {
        // ��ǰͼƬ
        protected $img;
        // ͼ��types ��Ӧ��
        protected $types = array(
                        1 => 'gif',
                        2 => 'jpg',
                        3 => 'png',
                        6 => 'bmp'
                    );
        // image
        public function __construct($img=''){
                !$img && $this->param($img);
        }
        // Info
        public function param($img){
                $this->img = $img;
                return $this;
        }
        // imageInfo
        public function getImageInfo($img){
                $info = @getimagesize($img);
                if(isset($this->types[$info[2]])){
                        $info['ext'] = $info['type'] = $this->types[$info[2]];
                } else{
                        $info['ext'] = $info['type'] = 'jpg';
                }
                $info['type'] == 'jpg' && $info['type'] = 'jpeg';
                $info['size'] = @filesize($img);
                return $info;
        }
        // thumb(��ͼ��ַ, ��, ��, �ü�, ����Ŵ�)
        public function thumb($filename,$new_w=1000,$new_h=1000,$cut=0,$big=0){
        // ��ȡԭͼ��Ϣ
        $info  = $this->getImageInfo($this->img);
        if(!empty($info[0])) {
            $old_w  = $info[0];
            $old_h  = $info[1];
            $type   = $info['type'];
            $ext    = $info['ext'];
            unset($info);
            // ���ԭͼ������ͼС ���Ҳ�����Ŵ�
            if($old_w < $new_h && $old_h < $new_w && !$big){
                    return false;
            }
            // �ü�ͼƬ
            if($cut == 0){ // �ȱ���
                    $scale = min($new_w/$old_w, $new_h/$old_h); // �������ű���
                    $width  = (int)($old_w*$scale); // ����ͼ�ߴ�
                    $height = (int)($old_h*$scale);
                    $start_w = $start_h = 0;
                    $end_w = $old_w;
                    $end_h = $old_h;
            } elseif($cut == 1){ // center center �ü�
                        $scale1 = round($new_w/$new_h,2);
                        $scale2 = round($old_w/$old_h,2);
                        if($scale1 > $scale2){
                                $end_h = round($old_w/$scale1,2);
                                $start_h = ($old_h-$end_h)/2;
                                $start_w  = 0;
                                $end_w    = $old_w;
                        } else{
                                $end_w  = round($old_h*$scale1,2);
                                $start_w  = ($old_w-$end_w)/2;
                                $start_h = 0;
                                $end_h   = $old_h;
                        }
                        $width = $new_w;
                    $height= $new_h;
                } elseif($cut == 2){ // left top �ü�
                        $scale1 = round($new_w/$new_h,2);
                    $scale2 = round($old_w/$old_h,2);
                    if($scale1 > $scale2){
                            $end_h = round($old_w/$scale1,2);
                            $end_w = $old_w;
                    } else{
                            $end_w = round($old_h*$scale1,2);
                            $end_h = $old_h;
                    }
                    $start_w = 0;
                    $start_h = 0;
                    $width = $new_w;
                    $height= $new_h;
                }
            // ����ԭͼ    
            $createFun  = 'ImageCreateFrom'.$type;
            $oldimg     = $createFun($this->img);
            // ��������ͼ
            if($type!='gif' && function_exists('imagecreatetruecolor')){
                $newimg = imagecreatetruecolor($width, $height);
            } else{
                $newimg = imagecreate($width, $height);
            }
            // ����ͼƬ
            if(function_exists("ImageCopyResampled")){
                    ImageCopyResampled($newimg, $oldimg, 0, 0, $start_w, $start_h, $width, $height, $end_w,$end_h);
            } else{
                ImageCopyResized($newimg, $oldimg, 0, 0, $start_w, $start_h, $width, $height, $end_w,$end_h);
            }
            // ��jpegͼ�����ø���ɨ��
            $type == 'jpeg' && imageinterlace($newimg,1);
            // ����ͼƬ 
            $imageFun = 'image'.$type;
            !@$imageFun($newimg,$filename) && die('����ʧ��!���Ŀ¼�Ƿ���ڲ��ҿ�д?');
            ImageDestroy($newimg);
            ImageDestroy($oldimg);
            return $filename;
        }
        return false;
    }
    // water(�����ַ,ˮӡͼƬ,ˮӡλ��,͸����)
        public function water($filename,$water,$pos=0,$pct=50){
                // ����ˮӡͼƬ
                $info = $this->getImageInfo($water);
                if(!empty($info[0])){
                        $water_w = $info[0];
                        $water_h = $info[1];
                        $type = $info['type'];
                        $fun  = 'imagecreatefrom'.$type;
                        $waterimg = $fun($water);
                } else{
                        return false;
                }
                // ���ر���ͼƬ
                $info = $this->getImageInfo($this->img);
                if(!empty($info[0])){
                        $old_w = $info[0];
                        $old_h = $info[1];
                        $type  = $info['type'];
                        $fun   = 'imagecreatefrom'.$type;
                        $oldimg = $fun($this->img);
                } else{
                        return false;
                }
                // ����ˮӡ
                $water_w >$old_w && $water_w = $old_w;
                $water_h >$old_h && $water_h = $old_h;                    
                // ˮӡλ��
                switch($pos){
                        case 0://���
                    $posX = rand(0,($old_w - $water_w));
                    $posY = rand(0,($old_h - $water_h));
                    break;
                case 1://1Ϊ���˾���
                    $posX = 0;
                    $posY = 0;
                    break;
                case 2://2Ϊ���˾���
                    $posX = ($old_w - $water_w) / 2;
                    $posY = 0;
                    break;
                case 3://3Ϊ���˾���
                    $posX = $old_w - $water_w;
                    $posY = 0;
                    break;
                case 4://4Ϊ�в�����
                    $posX = 0;
                    $posY = ($old_h - $water_h) / 2;
                    break;
                case 5://5Ϊ�в�����
                    $posX = ($old_w - $water_w) / 2;
                    $posY = ($old_h - $water_h) / 2;
                    break;
                case 6://6Ϊ�в�����
                    $posX = $old_w - $water_w;
                    $posY = ($old_h - $water_h) / 2;
                    break;
                case 7://7Ϊ�׶˾���
                    $posX = 0;
                    $posY = $old_h - $water_h;
                    break;
                case 8://8Ϊ�׶˾���
                    $posX = ($old_w - $water_w) / 2;
                    $posY = $old_h - $water_h;
                    break;
                case 9://9Ϊ�׶˾���
                    $posX = $old_w - $water_w;
                    $posY = $old_h - $water_h;
                    break;
                default: //���
                    $posX = rand(0,($old_w - $water_w));
                    $posY = rand(0,($old_h - $water_h));
                    break;
                }
            // �趨ͼ��Ļ�ɫģʽ
                imagealphablending($oldimg, true);
                // ���ˮӡ
                imagecopymerge($oldimg, $waterimg, $posX, $posY, 0, 0, $water_w,$water_h,$pct);
                $fun = 'image'.$type;
                !@$fun($oldimg, $filename) && die('����ʧ��!���Ŀ¼�Ƿ���ڲ��ҿ�д?');
                  imagedestroy($oldimg);
                  imagedestroy($waterimg);
                  return $filename;
        }
}
?>