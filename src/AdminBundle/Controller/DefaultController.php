<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function loadAction(){
        $etablissement = $this->getDoctrine()->getRepository('GenericBundle:Etablissement')->findAll();
        $ecoles = array();
        $societes = array();
        foreach($etablissement as $item)
        {
            if($item->getTier()->getEcole() && !$item->getSuspendu())
            {
                array_push($ecoles,$item);
            }
            else{
                array_push($societes,$item);
            }
        }
        $users = $this->getDoctrine()->getRepository('GenericBundle:User')->findAll();
        $licences = $this->getDoctrine()->getRepository('GenericBundle:Licencedef')->findAll();
        $qcms = $this->getDoctrine()->getRepository('GenericBundle:Qcmdef')->findAll();

        return $this->render('AdminBundle::AdminHome.html.twig',array('ecoles'=>$ecoles,'users'=>$users,'AllLicences'=>$licences,'societes'=>$societes,'qcms'=>$qcms));
    }

    public function loadiframeAction()
    {
        return $this->render('AdminBundle:Admin:iFrameContent.html.twig');
    }

    public function affichageAction($id)
    {
        $licencedef = $this->getDoctrine()->getRepository('GenericBundle:Licencedef')->findAll();
        $etablissement = $this->getDoctrine()->getRepository('GenericBundle:Etablissement')->find($id);
        if($etablissement->getTier()->getLogo())
        {
            $etablissement->getTier()->setLogo(base64_encode(stream_get_contents($etablissement->getTier()->getLogo())));
        }
        if($etablissement->getTier()->getFondecran())
        {
            $etablissement->getTier()->setFondecran(base64_encode(stream_get_contents($etablissement->getTier()->getFondecran())));
        }
        $licences = $this->getDoctrine()->getRepository('GenericBundle:Licence')->findBy(array('tier'=>$etablissement->getTier() ));
        $users = array();
        $users = array_merge($users,$this->getDoctrine()->getRepository('GenericBundle:User')->findBy(array('tier'=>$etablissement->getTier() )));
        $users = array_merge($users,$this->getDoctrine()->getRepository('GenericBundle:User')->findBy(array('etablissement'=>$etablissement )));
        $tiers = $this->getDoctrine()->getRepository('GenericBundle:Tier')->findAll();
        return $this->render('AdminBundle:Admin:iFrameContent.html.twig',array('licencedef'=>$licencedef,'etablissement'=>$etablissement,
            'libs'=>$licences,'tiers'=>$tiers,'users'=>$users));
    }

    public function creeNewModeleAction()
    {
        $modeles = array();
        if ($handle = opendir('../src/GenericBundle/Resources/views/Mail')) {

            while (false !== ($entry = readdir($handle))) {

                if ($entry != "." && $entry != "..") {

                    array_push($modeles,$entry);
                }
            }

            closedir($handle);
        }
        return $this->render("AdminBundle:Admin:creeNewModele.html.twig",array('modeles'=>$modeles));
    }
    public function saveNewModeleAction(Request $request)
    {
        $myfile = fopen("./templates/". $request->get('_filename') .".html.twig","w");
        fwrite($myfile,$this->render('GenericBundle:Mail:'. $request->get('_modele'),array('Textarea'=>$request->get('_newtext')))->getContent());
        fclose($myfile);
        return $this->render("AdminBundle:Admin:iFrameContent.html.twig");
    }
}