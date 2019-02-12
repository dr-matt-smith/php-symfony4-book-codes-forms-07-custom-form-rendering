<?php

namespace App\Controller;

use App\Entity\Student;
use App\Repository\StudentRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Form\StudentType;

class StudentController extends AbstractController
{
    /**
     * @Route("/student/new", name="student_new", methods={"POST", "GET"})
     */
    public function newAction(Request $request)
    {
        // create a task and give it some dummy data for this example
        $student = new Student();

        // create form from form class StudentType
        $form = $this->createForm(StudentType::class, $student);

        // if was POST submission, extract data and put into '$student'
        $form->handleRequest($request);

        // if SUBMITTED & VALID - go ahead and create new object
        if ($form->isSubmitted() && $form->isValid()) {
            return $this->createAction($student);
        }

        // render the form for the user
        $template = 'student/new.html.twig';
        $argsArray = [
            'form' => $form->createView(),
        ];

        return $this->render($template, $argsArray);
    }

    /**
     * @Route("/student", name="student_list")
     */
    public function list()
    {
        $studentRepository = $this->getDoctrine()->getRepository('App:Student');
        $students = $studentRepository->findAll();

        $template = 'student/list.html.twig';
        $args = [
            'students' => $students
        ];
        return $this->render($template, $args);
    }

    public function createAction(Student $student)
    {
        // entity manager
        $em = $this->getDoctrine()->getManager();

        // tells Doctrine you want to (eventually) save the Product (no queries yet)
        $em->persist($student);

        // actually executes the queries (i.e. the INSERT query)
        $em->flush();

        return $this->list();
    }



    /**
     * @Route("/student/{id}", name="student_show")
     */
    public function show(Student $student)
    {

        $template = 'student/show.html.twig';
        $args = [
            'student' => $student
        ];

        if (!$student) {
            $template = 'error/404.html.twig';
        }

        return $this->render($template, $args);
    }

    /**
     * @Route("/student/delete/{id}")
     */
    public function deleteAction(Student $student)
    {
        // store ID so we can still refer to it after object/row deleted
        $id = $student->getId();

        $em = $this->getDoctrine()->getManager();

        // tells Doctrine you want to (eventually) delete the Student (no queries yet)
        $em->remove($student);

        // actually executes the queries (i.e. the DELETE query)
        $em->flush();

        return new Response('Deleted student with id = '.$id);
    }

    /**
     * @Route("/student/update/{id}/{newFirstName}/{newSurname}")
     */
    public function updateAction(Student $student, $newFirstName, $newSurname)
    {
        $em = $this->getDoctrine()->getManager();

        $student->setFirstName($newFirstName);
        $student->setSurname($newSurname);
        $em->flush();

        return $this->redirectToRoute('student_show', [
            'id' => $student->getId()
        ]);

//        return $this->redirectToRoute('homepage');
    }
}
