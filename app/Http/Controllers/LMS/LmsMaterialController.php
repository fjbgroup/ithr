<?php

namespace App\Http\Controllers\LMS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TrainingCourse;
use App\Models\LmsMaterial;
use App\Models\LmsQuizQuestion;
use Illuminate\Support\Facades\Storage;

class LmsMaterialController extends Controller
{
    public function create(TrainingCourse $course)
    {
        return view('lms.materials.create', compact('course'));
    }

    public function store(Request $request, TrainingCourse $course)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:video,pdf,quiz',
            'file' => 'nullable|file|mimes:pdf,mp4,mov,avi|max:51200', // 50MB max
            'content' => 'nullable|string',
        ]);

        $material = new LmsMaterial();
        $material->course_id = $course->id;
        $material->title = $request->title;
        $material->type = $request->type;
        $material->content = $request->content;

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('lms_materials', 'public');
            $material->file_path = $path;
        }

        $material->order = LmsMaterial::where('course_id', $course->id)->max('order') + 1;
        $material->save();

        if ($request->type === 'quiz' && $request->has('questions')) {
            foreach ($request->questions as $q) {
                $options = array_filter($q['options'] ?? [], fn($opt) => trim($opt) !== '');
                $correctIndex = $q['correct_index'] ?? null;
                
                if (!empty($q['question']) && count($options) >= 2 && $correctIndex !== null && isset($options[$correctIndex])) {
                    LmsQuizQuestion::create([
                        'material_id' => $material->id,
                        'question' => $q['question'],
                        'options' => array_values($options),
                        'correct_answer' => trim($options[$correctIndex])
                    ]);
                }
            }
        }

        return redirect()->route('lms.courses.show', $course->id)->with('success', 'Material added successfully.');
    }

    public function edit(TrainingCourse $course, LmsMaterial $material)
    {
        return view('lms.materials.edit', compact('course', 'material'));
    }

    public function update(Request $request, TrainingCourse $course, LmsMaterial $material)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'nullable|file|mimes:pdf,mp4,mov,avi|max:51200',
            'content' => 'nullable|string',
        ]);

        $material->title = $request->title;
        $material->content = $request->content;

        if ($request->hasFile('file')) {
            if ($material->file_path) {
                Storage::disk('public')->delete($material->file_path);
            }
            $path = $request->file('file')->store('lms_materials', 'public');
            $material->file_path = $path;
        }

        $material->save();

        if ($material->type === 'quiz' && $request->has('questions')) {
            $material->questions()->delete();
            foreach ($request->questions as $q) {
                $options = array_filter($q['options'] ?? [], fn($opt) => trim($opt) !== '');
                $correctIndex = $q['correct_index'] ?? null;
                
                if (!empty($q['question']) && count($options) >= 2 && $correctIndex !== null && isset($options[$correctIndex])) {
                    LmsQuizQuestion::create([
                        'material_id' => $material->id,
                        'question' => $q['question'],
                        'options' => array_values($options),
                        'correct_answer' => trim($options[$correctIndex])
                    ]);
                }
            }
        }

        return redirect()->route('lms.courses.show', $course->id)->with('success', 'Material updated successfully.');
    }

    public function destroy(TrainingCourse $course, LmsMaterial $material)
    {
        if ($material->file_path) {
            Storage::disk('public')->delete($material->file_path);
        }
        $material->delete();
        return redirect()->route('lms.courses.show', $course->id)->with('success', 'Material deleted successfully.');
    }

    public function results(TrainingCourse $course, LmsMaterial $material)
    {
        if ($material->type !== 'quiz') {
            return redirect()->route('lms.courses.show', $course->id)->with('error', 'Only quizzes have results.');
        }

        $results = \App\Models\LmsProgress::with('staff')
            ->where('material_id', $material->id)
            ->whereNotNull('score')
            ->get();

        return view('lms.materials.results', compact('course', 'material', 'results'));
    }
}
