<?php

namespace Judgement\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Judgement\Http\Controllers\Controller;
use Judgement\Contest;
use Judgement\Submission;
use Judgement\Problem;
use Judgement\Language;

class AdminSubmission extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function submission(Request $request, $id)
    {
        $contest = Contest::findOrFail($id);

        $user = $request->query('u', 0);
        $problem = $request->query('p', 0);

        $submissions = Submission::where('contest_id', '=', $id);
        if ($user != 0) $submissions->where('user_id', $user);
        if ($problem != 0) $submissions->where('problem_id', $problem);
        $submissions = $submissions->orderBy('id', 'DESC')->paginate(15);

        return view('admin/submissions', [
            'pid' => $problem,
            'uid' => $user,
            'users' => $contest->users,
            'problems' => $contest->problems,
            'contest' => $contest,
            'submissions' => $submissions
        ]);
    }

    public function submissionView($id, $sub)
    {
        $contest = Contest::findOrFail($id);
        $submission = Submission::findOrFail($sub);

        $problem = Problem::find($submission->problem_id);
        $language = Language::find($submission->language_id);

        $source = storage_path(
            'contest/' . $id .
            '/problem/' . $problem->id .
            '/' . $submission->user->id .
            '/' . $submission->id .
            '/' . $submission->filename);
        $code = file_get_contents($source);

        return view('admin/submission', [
            'contest' => $contest,
            'problem' => $problem,
            'submission' => $submission,
            'language' => $language,
            'code' => $code
        ]);
    }
}
