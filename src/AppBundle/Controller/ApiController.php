<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class ApiController extends Controller
{

    /**
     * @Route("/api/properties", name="create_property")
     * @Method({"POST"})
     */
    public function createAction() {

    }



    /**
     * @Route("/api/properties", name="read_properties")
     * @Method({"GET"})
     */
    public function readAllAction() {

    }


    /**
     * @Route("/api/property/{id}", name="read_property")
     * @Method({"GET"})
     */
    public function readOneAction() {

    }


    /**
     * @Route("/api/properties/{id}", name="update_property")
     * @Method({"PUT"})
     */
    public function updateAction() {

    }


    /**
     * @Route("/api/properties", name="delete_property")
     * @Method({"DELETE"})
     */
    public function deleteAction() {

    }

}
