<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use AppBundle\Entity\EduMat;

class DefaultControllerOld extends Controller
{
    /**
     * @Route("/EduMat", name="EduMat")
     */
    public function indexAction(Request $request)
    {
        $edu = new EduMat();

        $form = $this->createFormBuilder($edu)
            ->add('name', TextType::class)
            ->add('status', ChoiceType::class, array(
                'choices' => array('Pending Distribution' => true, 'Distribution in progress' => false, 'Complete' => false)))
            ->add('dateCreated', null)
            ->add('dateFinished', null)
            ->add('recipient', TextType::class)
            ->add('description', TextType::class)
            ->add('add', SubmitType::class, array('label' => 'Add'))
            ->add('reset', ResetType::class, array('label' => 'Cancel'))
            ->getForm();


        // replace this example code with whatever you need
        return $this->render('default/index.html2.twig', array(
            'form' => $form->createView(),
        ));
    }
}