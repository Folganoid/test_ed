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

        if (!$this->getHeadersDataFromRequest($request)) {
            return new Response('Access denied', 401);
        };

        $em = $this->getDoctrine()->getManager();
        $prop = new Properties();
        $prop->setName($data->name);
        $prop->setDescription($data->description);
        $em->persist($prop);
        $em->flush();

        return new Response('Property created', 200);
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
            return new Response('Data not find', 204);
        }

        return $this->render('home.html.twig', ['props' => $props]);
    }

    /**
     * @Route("/api/properties/{id}", name="read_property")
     * @Method({"POST"})
     */
    public function readOneAction(Request $request, $id)
    {
        if (!$this->getHeadersDataFromRequest($request)) {
            return new Response('Access denied', 401);
        };

        $prop = $this->getDoctrine()
            ->getRepository(Properties::class)
            ->find($id);

        if (!$prop) {
            return new Response('Data not find', 204);
        }

        $response = (['id' => $prop->getId(), 'name' => $prop->getName(), 'description' => $prop->getDescription()]);

        return new JsonResponse($response);
    }

    /**
     * @Route("/api/properties/{id}", name="update_property")
     * @Method({"PUT"})
     */
    public function updateAction(Request $request, $id)
    {
        if (!$this->getHeadersDataFromRequest($request)) {
            return new Response('Access denied', 401);
        };

        $data = json_decode($request->getContent());
        $prop = $this->getDoctrine()
            ->getRepository(Properties::class)
            ->find($id);

        if (!$prop) {
            return new Response('Data not find', 204);
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
    public function deleteAction(Request $request, $id)
    {
        if (!$this->getHeadersDataFromRequest($request)) {
            return new Response('Access denied', 401);
        };

        $prop = $this->getDoctrine()
            ->getRepository(Properties::class)
            ->find($id);

        if (!$prop) {
            return new Response('Data not find', 204);
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($prop);
        $em->flush();

        return new Response('Property deleted', 200);
    }

    /**
     * create inner Token
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

    /**
     * check equal out coming Token & inner Token
     *
     * @param $outComeToken
     * @param $innerToken
     * @return bool
     */
    public function checkEqualToken($outComeToken, $innerToken): bool
    {
        if ($outComeToken != $innerToken) {  /////// change to == for avoid token matchig
            return false;
        }
        return true;
    }

    /**
     * get headers from request
     *
     * @param Request $request
     * @return bool
     */
    public function getHeadersDataFromRequest(Request $request): bool
    {
        $body = $request->getContent();
        $userId = $request->server->get('HTTP_X_USER_ID');
        $userToken = $request->server->get('HTTP_X_AUTH_TOKEN');
        $method = $request->server->get('HTTP_METHOD');
        $uri = $request->server->get('HTTP_URL');
        $secretKey = $this->container->getParameter('secret');
        $timeStamp = (new \DateTime(date('Y-m-d')))->getTimestamp();

        $token = $this->signRequest($method, $uri, $body, $timeStamp, $secretKey);

        return ($this->checkEqualToken($userToken, $token) && $userId) ? true : false;
    }

    /**
     * test login check
     *
     * @Route("/api/token", name="get_token")
     * @Method({"POST"})
     */
    public function getTokenTMP(Request $request)
    {
        $body = $request->getContent();
        $method = $request->server->get('HTTP_METHOD');
        $uri = $request->server->get('HTTP_URL');
        $secretKey = $this->container->getParameter('secret');
        $timeStamp = (new \DateTime(date('Y-m-d')))->getTimestamp();

        $token = $this->signRequest($method, $uri, $body, $timeStamp, $secretKey);

        return new Response($token, 200);
    }
}
