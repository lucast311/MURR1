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

/*
 * A Controller class that will build the EduMatForm, and display it.
 * When the form submits, this page will see if it was valid.
 * If the form was valid then add the data to the database as a new record.
 * If the data is invalid then the form will generate error messages based
 *  on the messages defined in the EduMat entity's Assertion statements.
 */
class EduMatFormController extends Controller
{
    /**
     * A function that does everything specified in the Class comment above
     * 
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

            // create a message to display to the user if the form submitted successfully
            $this->addFlash(
                    'success',
                    'Successfully Added'
            );

            // return to form page in case CSR wants to add another record
            return $this->redirectToRoute('EduMatForm');
        }

        // render the form page so the user can see it
        return $this->render('default/EduMatForm.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}