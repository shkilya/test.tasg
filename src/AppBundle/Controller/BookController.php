<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Book;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use FOS\RestBundle\FOSRestBundle;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Patch;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;


class BookController extends FOSRestController
{
    /** @var \JMS\Serializer\Serializer  */
    protected $serializer;

    public function __construct()
    {
        $this->serializer = \JMS\Serializer\SerializerBuilder::create()->build();
    }

    /**
     * @Get("/books/{id}", name="get_book")
     * @param integer $id
     * @return JsonResponse
     */
    public function getBookAction($id)
    {

        /** @var Book $book */
        $book = $this->getDoctrine()
            ->getRepository(Book::class)
            ->find($id);

        if (!$book) {
            return new JsonResponse(['message' => 'Book not found']);
        }

        $jsonContent = $this->serializer->serialize($book, 'json');

        return new Response($jsonContent,200);
    }

    /**
     * @Post("/books/create", name="post_book")
     * @param Request $request *
     * @return JsonResponse
     */
    public function postBookAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $book = new Book();
        $book->setName($request->get('name'));
        $book->setDescription($request->get('description'));

        $em->persist($book);
        $em->flush();

        $jsonContent = $this->serializer->serialize($book, 'json');

        return new Response($jsonContent,200);

    }

    /**
     * @Patch("/books/{id}",name="patch_book")
     * @param Request $request
     * @param         $id
     * @return JsonResponse
     */
    public function patchBookAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $book = $em->getRepository(Book::class)->find($id);
        if (!$book) {
            return new JsonResponse(['message' => 'Book not found']);
        }


        /*
         * this part of code is shit
         * for my opinion , request must be handle in something like formHandler
         * and this handler can return request validation
         * after formObject return  data and it put in doctrine handler and save
         */
        if($request->get('name')){
            $book->setName($request->get('name'));
        }
        if($request->get('description')) {
            $book->setDescription($request->get('description'));
        }

        $em->persist($book);
        $em->flush();

        $jsonContent = $this->serializer->serialize($book, 'json');

        return new Response($jsonContent,200);

    }
}