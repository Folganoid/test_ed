<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Properties;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends Controller
{

    /**
     * @Route("/api/properties", name="create_property")
     * @Method({"POST"})
     */
    public function createAction(Request $request)
    {
        $data = json_decode($request->getContent());

        $em = $this->getDoctrine()->getManager();
        $prop = new Properties();
        $prop->setName($data->name);
        $prop->setDescription($data->description);
        $em->persist($prop);
        $em->flush();

        return new Response('Property created', 200);

//        return new Response(var_dump($data->name));
    }


    /**
     * @Route("/api/properties", name="read_properties")
     * @Method({"GET"})
     */
    public function readAllAction()
    {
        $props = $this->getDoctrine()
            ->getRepository(Properties::class)
            ->findAll();

        if (!$props) {
            throw $this->createNotFoundException(
                'Can not find Properties'
            );
        }

        echo '<pre>';
        print_r($props);
        die();
        return new Response(var_dump($props));
    }


    /**
     * @Route("/api/properties/{id}", name="read_property")
     * @Method({"GET"})
     */
    public function readOneAction($id)
    {
        $prop = $this->getDoctrine()
            ->getRepository(Properties::class)
            ->find($id);

        if (!$prop) {
            throw $this->createNotFoundException(
                'Can`t find Property'
            );
        }

        return new Response(var_dump($prop));
    }


    /**
     * @Route("/api/properties/{id}", name="update_property")
     * @Method({"PUT"})
     */
    public function updateAction(Request $request, $id)
    {
        $data = json_decode($request->getContent());
        $prop = $this->getDoctrine()
            ->getRepository(Properties::class)
            ->find($id);

        if (!$prop) {
            throw $this->createNotFoundException(
                'Can`t find Property'
            );
        }

        $prop->setName($data->name);
        $prop->setDescription($data->description);

        $em = $this->getDoctrine()->getManager();
        $em->persist($prop);
        $em->flush();

        return new Response('Property updated', 200);
    }


    /**
     * @Route("/api/properties/{id}", name="delete_property")
     * @Method({"DELETE"})
     */
    public function deleteAction($id)
    {
        $prop = $this->getDoctrine()
            ->getRepository(Properties::class)
            ->find($id);

        $em = $this->getDoctrine()->getManager();
        $em->remove($prop);
        $em->flush();

        return new Response('Property deleted', 200);
    }

    /**
     *
     *
     * @param $method
     * @param $uri
     * @param $body
     * @param $timestamp
     * @param $secretKey
     * @return string
     */
    public function signRequest($method, $uri, $body, $timestamp, $secretKey)
    {
        $string = implode("\n", [
            $method,
            $uri,
            $body,
            $timestamp,
        ]);

        return hash_hmac('sha256', $string, $secretKey);
    }

}
