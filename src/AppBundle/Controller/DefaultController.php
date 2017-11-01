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

class DefaultController extends Controller
{
    /**
     * @Route("/EduMatForm", name="EduMatForm")
     */
    public function indexAction(Request $request)
    {
        $edu = new EduMat();

        $form = $this->createFormBuilder($edu)
            ->add('name', TextType::class)
            ->add('status', ChoiceType::class, array(
                'choices' => array('Pending Distribution' => 1, 'Distribution in progress' => 2, 'Complete' => 3)))
            ->add('dateCreated', DateType::class, array('widget' => 'single_text'))
            ->add('dateFinished', DateType::class, array('widget' => 'single_text'))
            ->add('recipient', TextType::class)
            ->add('description', TextType::class, array('required' => false))
            ->add('add', SubmitType::class, array('label' => 'Add'))
            ->add('reset', ResetType::class, array('label' => 'Cancel'))
            ->getForm();

        // replace this example code with whatever you need
        return $this->render('default/index.html2.twig', array(
            'form' => $form->createView(),
        ));
    }
}


//<?php

//namespace AppBundle\Controller;

//use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
//use Symfony\Bundle\FrameworkBundle\Controller\Controller;
//use Symfony\Component\HttpFoundation\Request;

//class DefaultController extends Controller
//{
//    /**
//     * @Route("/", name="homepage")
//     */
//    public function indexAction(Request $request)
//    {
//        // replace this example code with whatever you need
//        return $this->render('default/index.html.twig', [
//            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
//        ]);
//    }
//}




