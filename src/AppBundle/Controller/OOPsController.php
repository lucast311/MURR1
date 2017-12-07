<?php
namespace AppBundle\Controller;

use AppBundle\Entity\OOPs;
use AppBundle\Form\OOPsType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeExtensionGuesser;

class OOPsController extends Controller
{
    /**
     * Used to add a new OOPs notice via a form
     * @Route("/oops/add", name="oops_add")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {

        // create an OOPs and give it some dummy data for this example
        $oops = new OOPs('','');

        //generates a form
        $oopsForm = $this->createFormBuilder($oops)
                        ->add('binSerial', TextType::class,array(
                    'data' => ''
                    ))
            ->add('problemType', ChoiceType::class, array(
                    'choices' => OOPs::getProblemOptions()))
            ->add('description', TextType::class, array('required' => false))
            ->add('imageFile', FileType::class, array('required' => false))
            ->add('status', HiddenType::class, array(
                    'data' => 'Not yet started'))
            ->add('save', SubmitType::class, array('label' => 'Create OOPs Notice'))
            ->getForm(); 

        //request handler
        $oopsForm->handleRequest($request);

        //if the form, is submited and valid
        if($oopsForm->isSubmitted() && $oopsForm->isValid())
        {

            //form submition
            $em = $this->getDoctrine()->getManager();

            //writes the image to the uploads directory
            $oops->uploadImage();

            //add form data to oops table as a now OOPs notice
            $em->getRepository(OOPs::class)->insert($oops);

            //show success of form
            return $this->render('oops/addOOPsFormSuccess.html.twig');
        }

        return $this->render('oops/addOOPsForm.html.twig', array(
            'form' => $oopsForm->createView()
        ));
    }

}