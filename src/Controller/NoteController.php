<?php

namespace App\Controller;

use App\Entity\Note;
use App\Repository\NoteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/note')]
class NoteController extends AbstractController
{
    public function __construct(private readonly NoteRepository $noteRepository)
    {
    }

    #[Route('/', name: 'note_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('note/index.html.twig', [
            'notes' => $this->noteRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'note_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        if($request->getMethod() == "POST") {
            $note = $this->parseRequestToNote($request);
            $this->noteRepository->save($note, true);
            return $this->redirectToRoute('note_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('note/new.html.twig');
    }

    #[Route('/{id}', name: 'note_show', methods: ['GET'])]
    public function show(Note $note): Response
    {
        return $this->render('note/details.html.twig', [
            'note' => $note,
        ]);
    }

    #[Route('/{id}/edit', name: 'note_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Note $note): Response
    {
        if($request->getMethod() == "POST") {
            $note = $this->parseRequestToNote($request, $note);
            $this->noteRepository->save($note, true);
            return $this->redirectToRoute('note_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('note/edit.html.twig', [
            'note' => $note
        ]);
    }

    #[Route('/{id}', name: 'note_delete', methods: ['POST'])]
    public function delete(Request $request, Note $note): Response
    {
        if ($this->isCsrfTokenValid('delete'.$note->getId(), $request->request->get('_token'))) {
            $this->noteRepository->remove($note, true);
        }

        return $this->redirectToRoute('note_index', [], Response::HTTP_SEE_OTHER);
    }

    private function parseRequestToNote(Request $request, Note $note = null): Note
    {
        $form = $request->get('form');
        if (null === $note) {
            $note = new Note();
        }
        $note->setTitle($form['title'])
            ->setDescription($form['description']);

        return $note;
    }
}
