<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Paste;
use AppBundle\Repository\PasteRepository;
use Doctrine\ORM\EntityManager;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use AppBundle\Exceptions\BadRequestHttpException;

/**
 * @Route("/paste")
 */
class PasteController extends FOSRestController
{
    /**
     * @var PasteRepository
     */
    private $pasteRepository;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * PasteController constructor.
     * @param PasteRepository $pasteRepository
     * @param EntityManager $em
     */
    public function __construct(PasteRepository $pasteRepository, EntityManager $em)
    {
        $this->pasteRepository = $pasteRepository;
        $this->em = $em;
    }

    /**
     * @Route("/")
     * @Method({"GET"})
     * @Rest\View()
     */
    public function indexAction(Request $request)
    {
        return new View($this->pasteRepository->findAll());
    }

    /**
     * @Route("/")
     * @Method({"POST"})
     */
    public function createPaste(Request $request)
    {
        $content = $request->get('content');
        $password = $request->get('password');
        if(!is_string($content) || strlen($content) <= 0)
        {
            throw new BadRequestHttpException('The paste cannot be empty!');
        }

        $paste = new Paste();
        $paste->setContent($content);
        $paste -> setPassword($password);

        $this->em->persist($paste);
        $this->em->flush();

        return new View(['id' => $paste->getId()], 201);
    }


    /**
     * @Route("/{id}")
     * @Method({"GET"})
     */
    public function getPaste($id, Request $request)
    {
        $paste = $this->pasteRepository->find($id);
        $password = $request->get('password');
        if(!$paste || $password !=== $paste->getPassword()) {
            throw new BadRequestHttpException('Paste not found!');
        }
        return new View($paste);




    }
}
