<?php
namespace AppBundle\Controller;

use AppBundle\Entity\OOPs;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class OOPsController extends Controller
{
    /**
     * @Route("/OOPsForm")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        // create a task and give it some dummy data for this example
        $oops = new OOPs('1111111111','Damage');
        $oops->setStatus('Not in progress');

        $oopsForm = $this->createFormBuilder($oops)
            ->add('binSerial', TextType::class,array(
                    'attr' => array('pattern' => '/^[a-Z0-9]{10}$/', 'placeholder' => 'abcde12345')
                    ))
            ->add('problemType', ChoiceType::class, array(
                    'choices' => array(
                        'Damage' => 'Damage',
                        'Contamination' => 'Contamination',
                        'Blocked' => 'Blocked',
                        'Other (include in description)' => 'Other'
                    )))
            ->add('description', TextType::class, array('required' => false))
            ->add('image', FileType::class, array('required' => false))
            ->add('save', SubmitType::class, array('label' => 'Create OOPs Notice'))
            ->getForm();

        $oopsForm->handleRequest($request);

        if($oopsForm->isSubmitted() && $oopsForm->isValid())
        {
            //form submition

            return $this->render('default/OOPsFormSuccess.html.twig');
        }

        return $this->render('default/OOPsFormBase.html.twig', array(
            'form' => $oopsForm->createView(),
        ));
    }
}