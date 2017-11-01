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

class EduMatFormController extends Controller
{
    /**
     * @Route("/EduMatForm", name="EduMatForm")
     */
    public function indexAction(Request $request)
    {
        // Entity that the form will use
        $edu = new EduMat();

        // build the form
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

        // wait for response
        $form->handleRequest($request);

        // if submitted and valid
        if($form->isSubmitted() && $form->isValid())
        {
            // check value in 'status' to store a more human readable value in the database
            if($edu->getStatus() == 1)
            {
                $edu->setStatus('Pending Distribution');
            }
            else if($edu->getStatus() == 2)
            {
                $edu->setStatus('Distribution in progress');
            }
            else
            {
                $edu->setStatus('Complete');
            }

            // Add the valid form object to the database
            $em = $this->getDoctrine()->getManager();
            $em->persist($edu);
            $em->flush();

            // close the database
            $em->close();
            $em = null;

            $this->addFlash(
                    'success',
                    'Successfully Added'
            );

            // return to current page in case CSR wants to add another record
            return $this->redirectToRoute('EduMatForm');
        }

        // display the form for the first time
        return $this->render('default/index.html2.twig', array(
            'form' => $form->createView(),
        ));
    }
}