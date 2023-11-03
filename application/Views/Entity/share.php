<?php
/* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();

switch ($translate->getTargetLanguage()):
    case \Defines\Language::BE:
    case \Defines\Language::RU:
    case \Defines\Language::EN:
    case \Defines\Language::UK:
        $lang = $translate->getTargetLanguage();
        break;
    default:
        $lang = \Defines\Language::EN;
endswitch;
?>
<div class="<?php echo $this->get('class') ?> ya-share2" data-services="vkontakte,facebook,odnoklassniki,moimir,gplus" data-counter="" data-lang="<?php echo $lang ?>"></div>
<div class="<?php echo $this->get('class') ?> ya-share2" data-services="twitter,blogger,evernote,linkedin,lj,pocket,viber,whatsapp" data-limit="8"></div>
