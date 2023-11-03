<?php namespace Modules\Files;

/**
 * File processing controller
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 */
class Controller extends \Modules\AbstractController
{

    protected function initAllowed()
    {
        $access = new Permission($this->action);
        return $access->validate(
            $this->request->getRequestMethod(),
            $this->request->getResponseType()
        );
    }

    public function indexAction(array $aParams)
    {
        $iCode = \Defines\Response\Code::E_LOCKED;
        throw new \Error\Validation(
            \Defines\Response\Code::getHeader($iCode),
            $iCode
        );
    }

    public function indexNumAction(array $aParams)
    {
        $this->response->setLayoutType('file');
        $layout = new \Layouts\Helper\Zero($this->request, $this->response);
        $layout->add(null, (new Model)->getFile($aParams[0]));
        return $layout;
    }

    public function uploadAction(array $aParams)
    {
        $layout = new \Layouts\Helper\Basic($this->request, $this->response);

        $data = $this->input->getPost('data', null, FILTER_DEFAULT);
        $img = new \Data\File\Image($data);
        if ($img->isBlob()) {
            /** @todo convert image to $basicType (and resize if needed) */
            $id = (new \Data\ContentHelper)->saveBlob(
                'index',
                $aParams[0], // 'image#' . 
                $img->getContent()
            );
            $layout->add(null, "/files/$id");
        } else {
            $layout->add(null, $data);
        }

        return $layout;
    }
}
