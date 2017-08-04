<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Feed;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
 * Feed controller.
 *
 * @Route("feed")
 */
class FeedController extends Controller
{
    /**
     * Lists all feed entities.
     * 
     * @Route("/", name="feed_index")
     */
    public function indexAction(Request $request)
    {
        $defaultData = array('category' => '');
        $form = $this->createFormBuilder($defaultData)  
        ->add('category', TextType::class, ['required' => false])
        ->add('search', SubmitType::class, array('label' => 'Search'))
        ->getForm();

        $form->handleRequest($request);

        $category = '';
        $limit = $this->getParameter('pagination_limit');
        $page = $request->get('page', 1);
        if($form->isSubmitted() && $form->isValid()){
            $searchForm = $form->getData();
            $category = $searchForm['category'];
        }

        $em = $this->getDoctrine()->getRepository('AppBundle:Feed');
        $feeds = $em->getAllFeeds($category, $limit, $page);
        $totalRecord = $em->getTotalFeeds();

        $paginate = $this->__makePagination($page, $totalRecord, $limit);

        return $this->render('feed/index.html.twig', array(
            'feeds' => $feeds,
            'paginate' => $paginate,
            'form' => $form->createView()
        ));
    }

    /**
     * Creates a new feed entity.
     *
     * @Route("/new", name="feed_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $feed = new Feed();
        $form = $this->createForm('AppBundle\Form\FeedType', $feed);
        $form->add('create', SubmitType::class, [
            'label' => 'Create'
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($feed);
            $em->flush();

            return $this->redirectToRoute('feed_detail', array('id' => $feed->getId()));
        }

        return $this->render('feed/new.html.twig', array(
            'feed' => $feed,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a feed entity.
     *
     * @Route("/detail/{id}", name="feed_detail")
     * @Method("GET")
     */
    public function detailAction(Feed $feed)
    {
        $deleteForm = $this->createDeleteForm($feed);

        return $this->render('feed/show.html.twig', array(
            'feed' => $feed,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing feed entity.
     *
     * @Route("/edit/{id}", name="feed_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Feed $feed)
    {
        $deleteForm = $this->createDeleteForm($feed);
        $editForm = $this->createForm('AppBundle\Form\FeedType', $feed)
        ->add('edit', SubmitType::class, ['attr' => ['class' => 'btn btn-default']]);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('feed_edit', array('id' => $feed->getId()));
        }

        return $this->render('feed/edit.html.twig', array(
            'feed' => $feed,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a feed entity.
     *
     * @Route("/{id}", name="feed_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Feed $feed)
    {
        $form = $this->createDeleteForm($feed);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($feed);
            $em->flush();
        }

        return $this->redirectToRoute('feed_index');
    }

    /**
     * Creates a form to delete a feed entity.
     *
     * @param Feed $feed The feed entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Feed $feed)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('feed_delete', array('id' => $feed->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    private function __makePagination($curPage, $totalRecord, $limit){
        $paginate = [];

        $lastPage = ceil($totalRecord/$limit);
        $previousPage = $curPage > 1 ? $curPage - 1 : 1;
        $nextPage = $curPage < $lastPage ? $curPage + 1 : $lastPage;
        $paginate = [
            'curPage' => $curPage,
            'limit' => $limit,
            'lastPage' => $lastPage,
            'previousPage' => $previousPage,
            'nextPage' => $nextPage,
            'totalRecord' => $totalRecord
        ];

        return $paginate;
    }
}
