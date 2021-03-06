<?php

/** Contact photo upload and crop dialog. */
class contactsPhotoEditorAction extends waViewAction
{
    /**
     * @var waContact
     */
    protected $contact;
    public function execute()
    {
        $id = $this->getId();
        $this->contact = $contact = new waContact($id);

        // Show an uploaded image to crop?
        if (waRequest::get('uploaded')) {
            // Is there an uploaded file in session?
            $photoEditors = $this->getStorage()->read('photoEditors');
            if (isset($photoEditors[$id]) && file_exists($photoEditors[$id])) {
                $url = $this->getPreviewUrl(basename($photoEditors[$id]));
                $this->view->assign('oldPreview', $url);
                $this->view->assign('oldImage', $url);
            }
        } else {
            // Is there a photo for this contact?
            $filename = wa()->getDataPath("photo/$id/{$contact['photo']}.original.jpg", TRUE);
            if (file_exists($filename)) {
                $this->view->assign('oldPreview', $contact->getPhoto());
                $this->view->assign('oldImage', $contact->getPhoto('original'));
                $this->view->assign('orig', 1);
            }
        }

        $this->view->assign('contactId', $id);
        $this->view->assign('logged_user_id', wa()->getUser()->getId());
        $this->view->assign('contact', $contact);
        $this->view->assign('env', wa()->getEnv());
        $this->assignUrls();
    }

    protected function getId()
    {
        return (int)waRequest::get('id');
    }

    protected function getPreviewUrl($file)
    {
        return $this->getConfig()->getBackendUrl(true).'?app=contacts&action=data&temp=1&path=photo/'.$file;
    }

    protected function assignUrls()
    {
        $this->view->assign('tmpimage_url', '?module=photo&action=tmpimage');
        $this->view->assign('delete_url', '?module=photo&action=delete&id='.$this->contact->getId());
        $this->view->assign('crop_url', '?module=photo&action=crop');
        $this->view->assign('back_url', '#/contact/'.$this->contact->getId().'/');
    }
}

