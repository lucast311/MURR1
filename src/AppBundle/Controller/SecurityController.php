<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * This controller handles the logging in of users of the system
 */
class SecurityController extends Controller
{
    /**
     * Story 15a
     * Handles the login page
     *
     * @Route("/login", name="login")
     *
     * @param Request $request
     */
    public function loginAction(Request $request, AuthenticationUtils $authUtils)
    {
        //makes sure logged in users cant access the log in page
        if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED'))
        {
            return $this->redirect('/');
        }

        // Apparently the checking of the user is magic, and symfony handles this automatically in the background. Simply render the form and handle errors.
        // get the login error, if there is one
        $error = $authUtils->getLastAuthenticationError();

        // get the last username entered by the user, in case the page has an error
        $lastUsername = $authUtils->getLastUsername();

        // Render the twig
        return $this->render('security/login.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
        ));
    }

    /**
     * Story 15a
     * Handles the dummy restricted access page
     *
     * @Route("/forbidden", name="forbidden")
     *
     */
    public function forbiddenAction()
    {
        return $this->redirect('/');
    }
}