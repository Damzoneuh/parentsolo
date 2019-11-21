<?php

namespace App\Controller;

use App\Entity\Img;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ImageController extends AbstractController
{
    private $_serializer;

    public function __construct()
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $this->_serializer = new Serializer($normalizers, $encoders);
    }

    /**
     * @Route("/api/image", name="api_image")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     */
    public function receiveUserImg(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();
        /** @var UploadedFile $file */
        $file = $request->files->get('file');
        if (self::checkExtension($file)){
            $rand = self::genRandomName();
            $em = $this->getDoctrine()->getManager();
            $file->move($this->getParameter('storage.img'), $rand . '.' . $file->getClientOriginalExtension());
            $img = new Img();
            $img->setPath($this->getParameter('storage.img') . '/' . $rand . '.' . $file->getClientOriginalExtension());
            $img->setTitle($request->get('name'));
            $img->setIsProfile(false);
            $em->persist($img);
            $user->addImg($img);
            $em->flush();
        }
        return $this->json(['cool']);
    }

    /**
     * @param null $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/api/img/{id}", name="api_get_img", methods={"GET"})
     */
    public function getImg($id = null){
        if (!$id){
            $array = [];
            /** @var User $user */
            $user = $this->getUser();
            $imgs = $user->getImg()->getValues();
            foreach ($imgs as $img){
                /** @var Img $img */
                $content['path'] = $img->getPath();
                $content['title'] = $img->getTitle();
                $content['id'] = $img->getId();
                array_push($array, $content);
            }
            return $this->json($array);
        }
        $img = $this->getDoctrine()->getRepository(Img::class)->find($id);
        $content['id'] = $img->getId();
        $content['title'] = $img->getTitle();
        $content['path'] = $img->getPath();
        return $this->json($img);
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @Route("/api/img/render/{id}", name="api_render_img", methods={"GET"})
     */
    public function renderImg($id){
        $image = $this->getDoctrine()->getRepository(Img::class)->find($id);
        return $this->file($image->getPath());
    }

    private function checkExtension(UploadedFile $file){
        switch ($file->getClientMimeType()){
            case 'image/png':
            case 'image/gif':
            case 'image/jpeg':
            case 'image/jpg':
                return true;
                break;
            default:
                return false;
                break;
        }
    }

    /**
     * @return string
     * @throws \Exception
     */
    private static function genRandomName() : string {
        return bin2hex(random_bytes(12));
    }
}
