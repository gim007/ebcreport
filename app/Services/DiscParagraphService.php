<?php

namespace App\Services;

use App\Models\TestResult;

/**
 * Generates narrative paragraph text for each DISC report section.
 * Ported from legacy Calparagraph.php. All paragraph text is hardcoded;
 * only the participant name is user-supplied and is HTML-escaped before use.
 */
class DiscParagraphService
{
    public function generate(TestResult $result): array
    {
        $score    = $result->score();
        $mp       = $score->maskPercentile;
        $lp       = $score->latentPercentile;
        $fname    = e($result->participant->stud_fname ?? 'The participant');
        $lname    = e($result->participant->stud_lname ?? '');
        $gender   = $result->participant->stud_gender ?? 'Other';

        $g = $this->pronouns($gender);

        $dominant = ['D', 'I', 'S', 'C'][$score->dominantDimension()];

        return [
            'overview'           => $this->overview($mp, $lp, $fname, $g),
            'motivation'         => $this->motivation($mp, $fname, $g),
            'decision_making'    => $this->decisionMaking($mp, $fname, $g),
            'motivating'         => $this->motivatingFactors($mp, $fname, $g),
            'strengths'          => $this->strengths($mp, $fname, $g),
            'struggles'          => $this->struggles($lp, $fname, $g),
            'connecting'         => $this->connecting($mp, $fname, $g),
            'interpersonal'      => $this->interpersonal($mp, $fname, $g),
            'stress_profile'     => $this->stressProfile($lp, $fname, $g),
            'pressure_behavior'  => $this->pressureBehavior($lp, $fname, $g),
            'conflict_style'     => $this->conflictStyle($dominant, $fname, $g),
            'others_perception'  => $this->othersPerception($mp, $lp, $fname, $g),
            'working_with_d'     => $this->workingWith('D', $fname),
            'working_with_i'     => $this->workingWith('I', $fname),
            'working_with_s'     => $this->workingWith('S', $fname),
            'working_with_c'     => $this->workingWith('C', $fname),
        ];
    }

    // ── Helpers ─────────────────────────────────────────────────────────────

    private function pronouns(string $gender): array
    {
        if ($gender === 'Male') {
            return ['HHE' => 'He', 'HE' => 'he', 'HIS' => 'his', 'HIM' => 'him', 'HHIS' => 'His'];
        }
        return ['HHE' => 'She', 'HE' => 'she', 'HIS' => 'her', 'HIM' => 'her', 'HHIS' => 'Her'];
    }

    private function maskPattern(array $mp): int
    {
        $p = 0;
        foreach ($mp as $i => $v) {
            if ($v > 50) {
                $p += (int) pow(2, 3 - $i);
            }
        }
        return $p;
    }

    private function latentPattern(array $lp): int
    {
        return $this->maskPattern($lp);
    }

    private function averagePattern(array $mp, array $lp): int
    {
        $p = 0;
        for ($i = 0; $i <= 3; $i++) {
            if (($mp[$i] + $lp[$i]) / 2 > 50) {
                $p += (int) pow(2, 3 - $i);
            }
        }
        return $p;
    }

    // ── Sections ─────────────────────────────────────────────────────────────

    private function overview(array $mp, array $lp, string $f, array $g): string
    {
        ['HHE' => $HHE, 'HE' => $HE, 'HIS' => $HIS, 'HIM' => $HIM, 'HHIS' => $HHIS] = $g;
        return match ($this->averagePattern($mp, $lp)) {
            8  => "Ambitious, demanding and independent, {$f} is a motivated and direct person who is self-reliant and confident in {$HIS} own abilities. {$HHE} is committed to {$HIS} own goals, and will go to great lengths to achieve success in life. {$HHE} is impatient and exacting, expecting others to fall in with {$HIS} plans and reacting directly and forcefully when faced with obstacles.<br>{$f}'s powerful, sometimes even overbearing, style can be very difficult for others to deal with. {$HHE} operates best in positions where {$HE} is responsible for {$HIS} own work, or controls and co-ordinates other people.",
            4  => "Gregarious and expansive, {$f}'s style is dominated by the need for positive interaction with other people. Being highly communicative, {$HE} is extroverted, confident and persuasive. {$HHE} likes to feel that {$HE} has the appreciation of those around {$HIM}, and actively enjoys being the center of attention, especially in a social setting. {$HHIS} preference for positive relations with others means that {$HE} avoids direct confrontation with opposing viewpoints, preferring to employ {$HIS} powers of persuasion to defuse possible problems.<br>{$f}'s highly energetic, active approach means that {$HE} can, on occasion, act precipitately. {$HHE} will want to follow {$HIS} own ideas rapidly, and this can lead to {$HIS} acting without giving due consideration to possible consequences.",
            2  => "{$f} tends to adopt a quiet, unassuming demeanor and work slowly but consistently until a task is completed. {$HHE} is resistant to change and upheaval, preferring a steady, predictable environment. Because of this, {$HE} often shows great loyalty to those who help to maintain such an environment.<br>{$f}'s wish to avoid coming into conflict with others can often lead to {$HIS} becoming overburdened. While {$HE} does possess a certain natural resilience, {$f} needs more support than {$HIS} undemonstrative appearance might lead others to believe.",
            1  => "{$f} values ingenuity, logic and facts. {$HHE} relies on knowledge and certainty of {$HIS} position, and will hardly ever take an unnecessary risk or act purely on impulse. {$HHE} is naturally disposed to compromise — {$HE} understands the need to reach common ground, and will seek a mutual settlement rather than enter into confrontation with others.<br>Accuracy and precision are key factors in {$f}'s style — {$HE} focuses on the objective and the certain, rather than the subjective, and hence tends to disregard what {$HE} sees as purely emotional judgements from other people.",
            12 => "{$f}'s style can be effectively summarized with the single word, confidence. {$HHE} is assertive and extroverted, and {$HE} possesses both a drive towards success and a pro-active communicative style. {$HHE} thinks and acts quickly, and so {$HE} can respond well to changes in {$HIS} situation — indeed, {$HE} prefers a measure of variety and unpredictability.<br>{$HHIS} strong and persuasive style works best where {$HE} has a degree of control over {$HIS} own working conditions — {$HE} very much prefers to be responsible for {$HIS} own actions.",
            10 => "{$f} concentrates on efficiency and persistence. {$HHE} is resilient by nature, and will work hard to achieve a goal. {$HHE} possesses an extremely rare interpersonal style, usually related to some form of inner conflict — on one hand driven by an ambitious and competitive need for success, while on the other, preferring to avoid sudden change and upheaval.",
            9  => "{$f} is a practical and serious person who concentrates on matter-of-fact, quantifiable issues. {$HHE} is greatly concerned with effective procedures and satisfactory results, but considerably less interested in the emotional or personal side of life. Efficiency and speed of response are paramount to {$HIM}.<br>People who score like {$f} are typically rather suspicious of others' motives, and hence tend to avoid revealing information without carefully considering the possible repercussions.",
            6  => "{$f} is highly socially motivated, relying on the support, approval and attention of other people. {$HHE} acts on {$HIS} feelings and emotions, possessing strong communicative abilities, and is equally capable of communicating an idea or listening to others' points of view when necessary.<br>Despite these undoubted strengths, {$f} tends to over-emphasize the social side of {$HIS} approach. {$HHE} is less interested in matters of efficiency or productivity.",
            5  => "{$f} adapts {$HIS} style according to {$HIS} circumstances. Naturally friendly and co-operative, {$HE} prefers to work in a positive, open environment, and considers it important to build strong relationships with those around {$HIM}. {$HHE} also has a compliant element to {$HIS} style, preferring to follow rules and procedure rather than act independently.",
            3  => "{$f} has a very orderly and thoughtful persona; {$HE} is considerate, patient and systematic. {$HHIS} basic approach to life is passive — that is, {$HE} will tend to react to events initiated by others, rather than act directly {$HIM}self. Because of this underlying approach, {$HE} has a strong aversion to risk, and rarely acts if the outcome of a situation is in any doubt.",
            14 => "{$f} emphasizes independence and self-reliance. {$HHE} tends to think in terms of {$HIS} own aims and desires, and prefers at least some responsibility for {$HIS} own actions. {$HHE} has a social element to {$HIS} style, and likes to work closely with others, but this factor operates more on an interpersonal level than one of co-operativeness.",
            13 => "Energy is a defining characteristic of {$f}. {$HHE} thinks and acts quickly, and responds rapidly to changes of circumstance. {$HHE} is animated and enthusiastic, and while {$HE} values the approval and support of others, {$HE} is not dependent on them. {$HHE} is sensitive to subtleties that others might disregard, and adapts effectively to changes of environment.",
            default => "{$f}'s profile reflects a balanced combination of DISC dimensions, with strengths that adapt to a wide variety of situations.",
        };
    }

    private function motivation(array $mp, string $f, array $g): string
    {
        ['HHE' => $HHE, 'HE' => $HE, 'HIS' => $HIS, 'HIM' => $HIM, 'HHIS' => $HHIS] = $g;
        return match ($this->maskPattern($mp)) {
            8  => "The main driving force in {$f}'s style is the need for control and power, whether over others or over {$HIS} environment. {$HHE} will wish to be given the freedom to act independently, and prefers not to be under direct or constant supervision if this can be avoided.",
            4  => "{$f}'s motivation is strongly related to the ways in which {$HE} is seen by other people. {$HHE} is concerned about creating and maintaining a favorable impression with others, and this fact lies at the root of {$HIS} extroverted, enthusiastic style.",
            2  => "Amiable and persistent, {$f} will certainly be at {$HIS} best in an open and supportive environment, where time pressure is kept to a minimum and the environment is relatively free of sudden or unplanned change.",
            1  => "Rather passive by nature, {$f} likes to feel that {$HE} can rely on those around {$HIM} to help {$HIM} carry through {$HIS} plans and ideas. Questions of accuracy and caution are important to {$HIM}.",
            12 => "{$f} possesses a rapid and responsive style and, because of this, {$HE} is motivated by new experiences, change and excitement. Being both extroverted and assertive by nature, {$HE} will tend to take the initiative and expect others to follow {$HIS} lead.",
            10 => "A reliable and efficient individual, {$f} has a set of motivational factors that are often difficult to achieve in reality. To be at {$HIS} best, {$HE} likes to operate in a pressure-free situation in which {$HE} can interact positively with {$HIS} work colleagues.",
            9  => "If {$f} is to be motivated to follow a course of action, {$HE} will need to be convinced that it is a positive direction to follow. {$HHIS} practical turn of mind means that {$HE} will be far more interested in solid evidence, demonstrations and figures, than personal appeals.",
            6  => "{$f} is people-oriented and depends to an extent on others for motivation. {$HHE} much prefers to operate in an open, accepting environment, and has a need for positive interaction with those around {$HIM}.",
            5  => "{$f} needs to interact with others, both in social terms and on a more practical level, if {$HE} is to feel comfortable in {$HIS} position. {$HHE} looks to {$HIS} colleagues for friendship, and also for a sense of support in achieving results.",
            3  => "Because {$f} does not possess a particularly demonstrative or animated style, it is possible for {$HIM} to appear unmotivated even in situations where {$HE} is quite enthusiastic. {$HHIS} motivation will be enhanced considerably if {$HE} has a clear idea of ground rules, especially in terms of others' expectations.",
            14 => "The need for independence and freedom of action is a driving force behind {$f}'s style. {$HHE} is at {$HIS} best when {$HE} feels able to operate without having to obey constraints placed on {$HIM} by others.",
            13 => "{$f} becomes bored very quickly, and consequently desires plenty of variety and stimulation in {$HIS} work. {$HHIS} quick-thinking and adaptable approach means that {$HE} will quickly lose interest in repetitive work.",
            11 => "{$f} is a relatively self-contained individual, whose general approach to problems tends to be somewhat detached and dispassionate. {$HHE} prefers to work within a well organized system.",
            7  => "There is a wide range of positive elements in {$f}'s style, but one weakness is {$HIS} inability to deal well with pressure and conflict. {$HHE} finds aggressiveness and confrontation extremely problematic.",
            default => "{$f}'s motivational profile reflects a unique combination of internal and external drivers.",
        };
    }

    private function decisionMaking(array $mp, string $f, array $g): string
    {
        ['HHE' => $HHE, 'HE' => $HE, 'HIS' => $HIS, 'HIM' => $HIM, 'HHIS' => $HHIS] = $g;
        return match ($this->maskPattern($mp)) {
            8  => "{$f} is a driving and demanding decision maker who reaches {$HIS} decisions quickly. {$HHE} uses {$HIS} innate forcefulness and strength of will to ensure that {$HIS} conclusions are effectively implemented. {$HHE} focuses strongly on the efficient and practical, concentrating on these elements, sometimes at the expense of less quantifiable effects of {$HIS} decisions.",
            4  => "The focus of {$f}'s style lies in the realms of social contact. For {$HIM}, decision making is based on the need to foster positive feelings with others. {$HHE} will rarely make a decision that will cause unhappiness and hardship for other people, especially {$HIS} own acquaintances and friends.",
            2  => "The patient and steady style of {$f} means that {$HE} is able to make decisions in a measured, thoughtful manner, taking time to consider all options. Because {$HE} prefers to maintain the status quo, {$HIS} character contains a certain bias towards resisting change.",
            1  => "{$f} is a careful decision maker who will want to cover all possibilities and explore all avenues before committing {$HIM}self to a final decision. {$HHE} places value on precision and accuracy, and will want to analyze a situation scrupulously.",
            12 => "This active and energetic individual will not typically wait for decisions to be presented to {$HIM}, but will prefer to independently assess a situation. {$f}'s high levels of personal confidence means that {$HE} feels at ease reaching conclusions on only limited information.",
            10 => "{$f} will tend to make decisions on the basis of {$HIS} instincts and feelings. {$HHE} is capable of taking a measured view where a decision is of sufficient magnitude to warrant this. Persistent, and sometimes demanding, {$HE} is able to ensure that, once a decision has been reached, it will be carried out thoroughly and effectively.",
            9  => "Getting things right is of utmost importance to {$f}. {$HHE} will need to be absolutely sure that {$HE} is making the right choice before finalizing a decision. This means that the process is, for {$HIM}, one of extensive research and consultation.",
            6  => "{$f} has a social and amiable style, considerate of the views and feelings of others, and {$HIS} decision making process is no exception to this. It is a priority for {$HIM} to ensure that any decisions {$HE} might make will have minimal negative effects on {$HIS} colleagues.",
            5  => "The support of others, both practical and emotional, will be greatly valued by {$f} as {$HE} approaches a decision. {$HHIS} natural reaction is to avoid confrontation and to minimize risk, and so {$HIS} decisions will tend to be conservative in style.",
            3  => "{$f} sees decision making as an extension of the planning process, rather than an activity in its own right. Making instantaneous decisions based on an instinctive appraisal of a situation is almost impossible for {$f}.",
            14 => "{$f} possesses the ability to take a measured view of a problem. {$HHE} is also capable of reaching a conclusion in a shorter time-scale, using prior experience as a guide, where available. {$HHE} is generally unwilling to ask others for support as part of {$HIS} decision making process.",
            13 => "One word sums up {$f}'s approach to decision making: speed. {$HHE} will tend to quickly survey the available options and select a course of action rapidly, relying on {$HIS} experience and instincts.",
            11 => "While {$f} tends to approach decision making in a somewhat formal way, concentrating on details and practicalities, this does not mean that {$HE} is insensitive to the needs of other people, and {$HE} is capable of taking account of others' needs in coming to a decision.",
            7  => "Friendly and co-operative, {$f} prefers to see decision making in a democratic way, allowing everyone affected to make their input and produce a consensus, rather than reaching an individual conclusion.",
            default => "{$f}'s approach to decision making draws on multiple DISC dimensions.",
        };
    }

    private function motivatingFactors(array $mp, string $f, array $g): string
    {
        ['HHE' => $HHE, 'HE' => $HE, 'HIS' => $HIS, 'HIM' => $HIM, 'HHIS' => $HHIS] = $g;
        return match ($this->maskPattern($mp)) {
            8  => "People who score like {$f} feel that they are in control. {$f} seeks opportunities to reinforce and emphasize {$HIS} personal power. {$HHE} measures {$HIS} progress in life by {$HIS} achievements and successes. Being impatient and forthright, {$f} intensely dislikes situations that {$HE} is unable to directly resolve for {$HIM}self.",
            4  => "Highly influential individuals, like {$f}, are motivated by relationships with others. Specifically, they need to feel accepted by those around them and react badly if they perceive themselves to be rejected or disliked. Praise and approval make a strong impression on them.",
            2  => "The underlying patience of {$f} is the root of {$HIS} motivation. {$HHE} needs to feel that {$HE} has the support of those around {$HIM} and, more importantly, time to adapt to new situations. {$HHE} has an inherent dislike of change, and will prefer to maintain the status quo whenever possible.",
            1  => "There is one factor that has a more significant effect on {$f}'s motivation than any other — certainty. {$HHE} needs to feel completely sure of {$HIS} position, and of others' expectations of {$HIM}, before {$HE} is able to proceed. Because of this, {$HE} has a very strong aversion to risk.",
            12 => "Success and recognition are twin motivating factors for {$f}. To be content, {$HE} must feel that {$HE} is a success in both {$HIS} business and personal lives. More than this, {$HE} is motivated by challenge. Stagnation is anathema to {$f}.",
            10 => "The motivating factors for {$f} are associated with control, power, the need for certainty and the avoidance of change. These suggest a preference for a situation in which {$f} exercises whatever authority {$HE} may have to preserve the status quo and avoid sudden change.",
            9  => "This is a complex character in terms of motivation. {$f} has a desire for personal achievement and success, and also needs to feel that {$HE} is completing assignments or projects accurately and efficiently. {$HHIS} competing motivations may make {$HIM} appear stuck and unmoving.",
            6  => "Antagonism, rejection and confrontation are all situations that {$f} will try to avoid. To feel completely motivated, {$f} needs to feel that {$HE} is appreciated, respected and liked by the people around {$HIM}.",
            5  => "{$f}'s motivations are more complex than most, because of the opposing natures of {$HIS} preferred styles. On one hand, {$f} is interested in attracting the attention and approval of others. On the other hand, {$f} has a strong need to be certain and assured that {$HE} is right.",
            3  => "A consequence of the patient, precise style exhibited by {$f} is a need for time to plan and execute {$HIS} work to a standard with which {$HE} can feel satisfied. {$HHE} will also seek certainty, and need to be sure that the work that {$HE} is doing conforms with the expectations of {$HIS} colleagues.",
            14 => "Because of {$HIS} independent style, {$f} will seek to hold a degree of control over {$HIS} own circumstances, and will look for opportunities to drive towards {$HIS} own ambitions. While success is important, however, {$HE} also values positive relationships with other people.",
            13 => "{$f} has a complex set of motivating factors that may sometimes conflict with one another. In this case, motivation stems from the achievement of personal ambition, the acceptance and approval of other people, and certainty of their own position.",
            11 => "{$f} has a profile that demonstrates a variety of motivating factors. These include the achievement of results, time to adapt to changing situations, a full understanding of fact and detail and an avoidance of risk.",
            7  => "{$f} is not ambitious by nature. {$HHE} rarely has a specific set of goals or aims in life. Motivation for {$HIM} is more a matter of a general sense of happiness and contentment — specifically, the development of positive, warm relations with other people.",
            default => "{$f}'s motivating factors span multiple DISC dimensions in a unique combination.",
        };
    }

    private function strengths(array $mp, string $f, array $g): string
    {
        ['HHE' => $HHE, 'HE' => $HE, 'HIS' => $HIS, 'HIM' => $HIM, 'HHIS' => $HHIS] = $g;
        return match ($this->maskPattern($mp)) {
            8  => "{$f}'s strengths derive from {$HIS} dynamic and driving style; {$HE} is energetic, direct, responsive, independent and self-reliant. {$HHIS} sense of personal responsibility is very strong, to the extent that {$HE} will prefer to operate in an environment over which {$HE} has some level of personal control.",
            4  => "The advantages of {$f}'s style derive from {$HIS} strong social orientation. {$HHE} is highly gregarious, and is equally capable of forming and of maintaining relationships with those around {$HIM}. {$HHIS} warm, relaxed approach makes others feel at ease and ready to discuss matters both personal and practical.",
            2  => "{$f}'s profile represents stability. {$HHE} is a person who will work calmly and consistently, demanding little from those around {$HIM} but appreciating support when it is offered. {$HHE} operates well on a personal level with others.",
            1  => "{$f} has a cooperative style, open to the needs of those around {$HIM}, and ready to help with those needs if {$HE} can. {$HHIS} sensitive nature makes {$HIM} especially aware of others' requirements, and {$HE} is quick to respond to these perceptions.",
            12 => "{$f} has a highly independent attitude; {$HE} is not only able to take responsibility for {$HIS} own work, but is highly motivated to do so. {$HHE} also possesses a strong measure of confidence, and interacts well with other people on both a business and a social level.",
            10 => "{$f}'s style emphasizes qualities of effectiveness and resilience. {$HHE} is aware of time-scales and deadlines, and {$HE} is equally aware of the need to produce work of high quality, and is generally able to balance these two factors adequately.",
            9  => "The strengths of {$f}'s style lie in {$HIS} detached, impartial approach. {$HHE} is driving and ambitious, and is able to balance these elements of {$HIS} style with the need to produce reliable and accurate work of high quality.",
            6  => "This warm, open individual is receptive to the personal needs of others while maintaining a strong sense of social confidence. {$HHE} is equally comfortable fulfilling a socially active role as {$HE} is in being in a supportive one.",
            5  => "{$f} is capable of interacting with others on both a social and practical level. {$HHE} is generally willing and able to work as part of a team, accepting the decisions of the majority, or the team leader, regardless of {$HIS} personal views.",
            3  => "{$f} is very much a team player. {$HHE} sees tasks in terms of structured, co-ordinated solutions and while {$HE} probably has some natural problem-solving abilities, {$HIS} style is receptive enough for {$HIM} to listen to and accept the contributions of other people.",
            14 => "{$f} can work well without the instructions and support of others. {$HHE} is capable of adapting to new situations easily, and has the confidence and resilience to operate in untried situations.",
            13 => "Urgency is the keyword with regard to {$f}'s advantages. {$HHE} thinks and acts quickly and responsively, adapts to unexpected changes without undue difficulty, and possesses a heightened sensitivity to factors that others might simply not notice.",
            11 => "A steady, determined candidate, {$f} prefers to examine circumstances in a rational and unprejudiced way, taking little account of emotional factors or preconceptions. {$HHE} is disciplined and rational.",
            7  => "{$f}'s blend of interpersonal skills with a more cautious and patient side to {$HIS} nature provides a useful variety of talents. {$HHE} communicates effectively, but unlike some other socially-based styles, {$HE} is unlikely to take unwarranted risks or act precipitately.",
            default => "{$f}'s strengths reflect a balanced profile across the DISC dimensions.",
        };
    }

    private function struggles(array $lp, string $f, array $g): string
    {
        ['HHE' => $HHE, 'HE' => $HE, 'HIS' => $HIS, 'HIM' => $HIM, 'HHIS' => $HHIS] = $g;
        return match ($this->latentPattern($lp)) {
            8  => "Probably the most significant disadvantage of {$f}'s interpersonal style stems from {$HIS} highly independent nature. Because of this, {$HE} finds it hard to conform to rules and regulations imposed by others. {$HHE} can also be extremely blunt, and highly demanding of those around {$HIM}.",
            4  => "A flexible, relaxed approach to work, while having some positive aspects, also means that {$f} is not well adapted to dealing with structured situations. {$HHE} finds it difficult to fit easily into formal circumstances, and can sometimes be tempted to follow {$HIS} own ideas rather than comply precisely with the rules.",
            2  => "{$f}'s measured, steady approach must always be borne in mind when assigning tasks to {$HIM}. {$HHE} generally works reliably and productively, but {$HIS} preference to avoid change makes {$HIM} unsuitable for work in unpredictable environments.",
            1  => "{$f}'s need to be sure of {$HIS} position, coupled with {$HIS} natural reluctance to reveal {$HIS} feelings, means that {$HE} has a tendency to equivocate, especially when {$HE} is uncertain of {$HIS} position. {$HHE} tends to be a perfectionist.",
            12 => "{$f} is an active individual, who seldom doubts {$HIS} own actions. Sometimes, {$HE} fails to consider the consequences before committing {$HIM}self to a course of events. {$HHIS} dynamic, fast-paced style makes it difficult for {$HIM} to accept situations requiring more patient handling.",
            10 => "{$f}'s determined, stubborn approach and the tenacious manner in which {$HE} drives towards {$HIS} goals can be difficult for others to accept. {$HIS} doggedness in pursuit of {$HIS} aims often continues past the point of practicality.",
            9  => "{$f} is by nature suspicious of others' motives. {$HHE} has a rather pessimistic style that can at times verge on the cynical. {$HHE} desires success, and {$HIS} wishes to be in control of all aspects of {$HIS} surroundings can lead {$HIM} to see difficulties where none, in fact, exist.",
            6  => "{$f}'s most significant weakness is the difficulty {$HE} experiences in dealing with rejection. {$HHE} will look for positive reinforcement from those around {$HIM}, and if this support is not forthcoming, {$HE} will lose motivation.",
            5  => "Efficiency and the drive to succeed are qualities lacking in {$f}'s style. While {$HE} is effective in a supportive role, {$HE} rarely functions well as a command and control leader.",
            3  => "The most important disadvantages of {$f}'s style stem from {$HIS} dependence on the support of others for {$HIS} actions. If this support is not available, then {$HE} may fail to act in time. {$f}'s profile also suggests that {$HE} may be resistant to change.",
            14 => "Perhaps the greatest weakness of {$f}'s interpersonal style is {$HIS} tendency to disregard the needs and feelings of those around {$HIM}. In a work situation, this will usually manifest itself as an inclination to make decisions and act upon them without consulting others.",
            13 => "Because of the key element of rapid pace in {$f}'s style, there is a danger of {$HIS} acting without fully considering the consequences. This is especially true in terms of {$HIS} interactions with other people.",
            11 => "{$f} will often appear remote, sometimes even cold, to others. {$HHE} is more interested in the practicalities of planning and executing {$HIS} plans than {$HE} is in gaining the approval of others.",
            7  => "A lack of strong ambition or need for personal success means that {$f} can be seen as unmotivated by others. {$HHE} operates quite well on a social level, but is not naturally competitive or assertive.",
            default => "{$f}'s latent profile reveals some areas for growth that may not be immediately visible to others.",
        };
    }

    private function connecting(array $mp, string $f, array $g): string
    {
        ['HHE' => $HHE, 'HE' => $HE, 'HIS' => $HIS, 'HIM' => $HIM, 'HHIS' => $HHIS] = $g;
        return match ($this->maskPattern($mp)) {
            8  => "{$f}'s emphasis on achievement and success significantly influences {$HIS} relationships with other people. In {$HIS} efforts to get {$HIS} goals met, {$HE} may discount the feelings of others, leaving them with a sense of not being cared for.",
            4  => "Connecting with others is what people who score like {$f} thrive on. {$HHE} is open to others and confident in {$HIS} own social skills. {$HHE} allows {$HIM}self to enthusiastically engage with people in almost any situation and at any time.",
            2  => "{$f} will look to more socially assertive people to initiate relationships and contact. {$f}'s loyalty and dependability makes {$HIM} far more comfortable in maintaining interpersonal relations than starting them. For this reason, {$f}'s circle of friends and close acquaintances is often small and tightly-knit.",
            1  => "{$f} has many strengths but the skills to relate easily to other people is not likely one of them. {$f} may find it uncomfortable to engage with others on a purely social basis. Business relationships are formed with strong boundaries characterized with skepticism.",
            12 => "{$f}'s style is characterized by strong social skills and persuasive, goal-directed communications. {$HHE} is capable of great charm, but will sometimes slip into a demeanor that may be seen as demanding and overbearing if {$f} feels {$HIM}self to be under pressure.",
            10 => "It is difficult to predict the preferred social interaction style of {$f}. On one hand, {$f} is comfortable directing and confronting the behavior of others, and on the other hand, {$HE} likes to maintain an amiable and trusting relationship.",
            9  => "Relating to others on a personal level is not a high priority for {$f}. When communicating with other people is essential, {$f} tends to keep it brief and succinct. {$HHE} will focus on practical matters.",
            6  => "{$f} is most effective at relating to other people in an all-round sense. {$HHE} is able to socialize easily and {$HIS} gregarious nature allows {$HIM} to feel at ease with people {$HE} does not know.",
            5  => "The ways in which {$f} will relate to other people is highly dependent on the circumstances under which an encounter takes place. In a circle of friends, {$f} is capable of quite confident and extroverted behavior.",
            3  => "{$f} may appear passive and withdrawn and often finds it difficult to relate to other people, particularly in unfamiliar settings. {$HHE} needs to know exactly where {$HE} stands before {$HE} feels able to act.",
            14 => "{$f} interacts easily and skillfully with other people. {$HHE} possesses the personal self-confidence to mix relatively easily with strangers and in unusual situations. {$f} has a strong sense of independence, however, and is prepared to go to considerable lengths to maintain {$HIS} own sense of identity.",
            13 => "The ways in which {$f} relates to other people will vary according to {$HIS} social situation. In more social, casual circumstances, {$HE} will appear friendly and animated. If {$HIS} situation is more formal, however, a more direct and determined side of {$f} will come forward.",
            11 => "Relating to other people is not an area of particular emphasis for {$f}. Where {$HE} responds to others on more than a purely practical basis, {$HE} tends to be rather reserved in approach.",
            7  => "{$f} has significant strengths in interpersonal and communicative relations. Specifically, {$f} relates in an outgoing, friendly interpersonal style. {$HHE} confers capable listening skills and patiently explores issues with others.",
            default => "{$f} connects with others in ways that are shaped by {$HIS} unique DISC combination.",
        };
    }

    private function interpersonal(array $mp, string $f, array $g): string
    {
        ['HHE' => $HHE, 'HE' => $HE, 'HIS' => $HIS, 'HIM' => $HIM, 'HHIS' => $HHIS] = $g;
        return match ($this->maskPattern($mp)) {
            8  => "{$f}'s driving, competitive and ambitious approach give rise to {$HIS} communication style. {$HHE} will instinctively look for advantage in any situation where it might present itself. In communication, {$HE} allows {$HIS} assertive nature to be fully expressed, often dominating interaction with others.",
            4  => "{$f} possesses perhaps the ultimate in interpersonal conversation. {$HHE} relates to other people very easily and possesses a wide range of communications skills. {$HHE} is enthusiastic, open, charming and persuasive. The downside is that {$HE} likes to be the center of attention and may not deal easily with conflict or criticism.",
            2  => "{$f} enjoys the company of others, and has a pleasant social style, but {$HIS} profile shows that {$HE} is quite underconfident in social matters. {$HHIS} communication style will depend on {$HIS} situation — {$HE} will need to feel supported and encouraged to communicate easily.",
            1  => "People like {$f} tend to approach communication in a structured way, rather than as a means of social interaction. Mostly, {$f} is more interested in conveying and receiving factual information than in interacting on a personal level, and this can make {$HIM} appear cold or aloof.",
            12 => "Personalities that combine a strong assertiveness with interpersonal abilities often exhibit an ability to adapt their communication style to meet the needs of a particular situation. While {$f} can be expected to be extroverted and expressive at all times, {$HIS} style can change from relaxed and receptive to demanding.",
            10 => "{$f}'s unusual profile pattern reflects an individual that many people will find unpredictable. {$HHIS} style contains both a drive to succeed, and a need to avoid conflict. The ways that {$f} manages {$HIS} relationships will be highly dependent on the way in which {$HE} perceives {$HIS} environment.",
            9  => "{$f} is both systematic and demanding and {$HIS} communication style will reflect these attributes. {$HHIS} interaction with other people can typically be characterized as serious and forceful. {$HHE} also brings a sense of competition to {$HIS} communication.",
            6  => "{$f} has a very open communication style. {$HHE} is not reticent about approaching others. {$HHE} enjoys interacting with others on all levels, and freely discusses {$HIS} own ideas, experiences and feelings, but not to the detriment of considering other people's positions.",
            5  => "An adaptable social style combining a real interest in the ideas and feelings of others with a more sedate and practical side makes {$f} an effective and responsive communicator. This applies especially in situations where {$HE} feels confident and relaxed.",
            3  => "{$f} will tend to approach communication with others in a rather passive way, waiting to be asked for {$HIS} ideas rather than offering them directly. {$HHE} will rarely make a statement without giving due consideration to its effects and consequences.",
            14 => "{$f} has a highly adaptable interpersonal style, being able to adjust {$HIS} responses to meet the needs of a particular situation. There is a constant element in {$HIS} approach, however, which is {$HIS} sense of independence and individuality.",
            13 => "Qualities of urgency and pace dominate {$f}'s communication style. {$HHIS} communication will normally be aimed at some kind of tangible purpose. While fact and detail are important to {$f}, {$HHIS} sense of personal ambition also modulates {$HIS} mode of interaction with others.",
            11 => "{$f}'s communication style is very precise in nature. {$HHE} considers it important to make {$HIS} intentions absolutely plain. {$HHE} considers {$HIS} words carefully before speaking. {$HHIS} need for certainty that others have understood {$HIS} meaning often results in {$HIS} going to considerable lengths to ensure accuracy.",
            7  => "{$f} has a strongly socially-oriented style with a range of effective interpersonal skills. {$HHE} communicates easily with others on a social level, and also possesses a receptive side allowing {$HIM} to listen to and appreciate others' problems. Conflict is difficult for {$HIM} to deal with, however.",
            default => "{$f}'s communication style integrates multiple DISC dimensions.",
        };
    }

    private function stressProfile(array $lp, string $f, array $g): string
    {
        ['HHE' => $HHE, 'HE' => $HE, 'HIS' => $HIS, 'HIM' => $HIM] = $g;
        $THRESH = 18;
        $parts  = [];

        if (($lp[0] - $lp[1]) > $THRESH) {
            $parts[] = "{$f} tends to be logical, critical and incisive in {$HIS} approach to attaining goals. Problems requiring original and analytical effort provide the greatest challenge for {$f}. Generally, one would expect that {$HE} would be blunt and critical with other people but {$HE} will rarely hold a grudge.";
        }
        if (($lp[0] - $lp[2]) > $THRESH) {
            $parts[] = "{$f} responds quickly to a challenge and has mobility and flexibility in {$HIS} approach. {$HHE} tends to be a versatile self-starter who responds rapidly to competition.";
        }
        if (($lp[0] - $lp[3]) > $THRESH) {
            $parts[] = "{$f} responded like others who act positively and directly in the face of opposition. As a forceful individual, {$f} will take a stand and fight for {$HIS} position.";
        }
        if (($lp[1] - $lp[0]) > $THRESH) {
            $parts[] = "{$f} tends to behave in a poised, cordial manner displaying social aggressiveness in situations perceived to be favorable and unthreatening. {$HHE} tends to exude charm and strives to establish rapport at first contact with people.";
        }
        if (($lp[1] - $lp[2]) > $THRESH) {
            $parts[] = "{$f} tends to seek out people with enthusiasm and spark. As an outgoing person who displays a contagious optimism, {$HE} tries to win people through persuasiveness and emotional appeal.";
        }
        if (($lp[1] - $lp[3]) > $THRESH) {
            $parts[] = "{$f} displays self confidence in most all endeavors with others. Although always striving to win you over, {$HE} is reluctant to give up {$HIS} own point of view.";
        }
        if (($lp[2] - $lp[0]) > $THRESH) {
            $parts[] = "{$f} tends to be a steady, consistent individual who prefers to deal with one assignment at a time. Steady under most pressures, {$HE} strives to stabilize {$HIS} environment.";
        }
        if (($lp[2] - $lp[1]) > $THRESH) {
            $parts[] = "{$f} tends to be a patient, controlled individual. {$HHE} moves with moderation and deliberateness in most undertakings. Even under stress, {$HE} will usually project a relatively unruffled appearance.";
        }
        if (($lp[2] - $lp[3]) > $THRESH) {
            $parts[] = "{$f} tends to be a persistent, persevering individual who is not easily swayed once {$HIS} mind has been made up on any matter. {$HHE} will set {$HIS} own pace and stick with it.";
        }
        if (($lp[3] - $lp[0]) > $THRESH) {
            $parts[] = "{$f} tends to act in a careful, conservative manner and is generally willing to modify or compromise {$HIS} position in order to achieve {$HIS} goals. {$HHE} prefers an atmosphere free from antagonism and desires harmony.";
        }
        if (($lp[3] - $lp[1]) > $THRESH) {
            $parts[] = "{$f} is a stickler for system and order. {$HHE} makes decisions based on proven precedent and known facts.";
        }
        if (($lp[3] - $lp[2]) > $THRESH) {
            $parts[] = "This individual would be very much concerned with avoiding risk or trouble. {$HHE} tends to look for hidden meanings. Tension may be evident particularly when {$HE} is under stress for results.";
        }
        if (abs($lp[0] - $lp[3]) < 15) {
            $parts[] = "Since {$f} has an equal striving for accomplishment and quality, {$HE} is often seen as a perfectionist. In a positive sense, this is a person who will not accept an answer to a problem but strives for the best answer.";
        }

        if (empty($parts)) {
            $parts[] = "{$f}'s profile under stressful conditions indicates a relatively balanced response pattern, adapting fluidly to changing demands without strong predictable reactions.";
        }

        return implode(' ', $parts);
    }

    /** S-14 Behavior Under Pressure & Stress — pattern-driven on the Latent profile. */
    private function pressureBehavior(array $lp, string $f, array $g): string
    {
        ['HHE' => $HHE, 'HE' => $HE, 'HIS' => $HIS, 'HIM' => $HIM] = $g;
        return match ($this->latentPattern($lp)) {
            8  => "Under pressure, {$f} drives harder and acts faster. {$HHE} narrows {$HIS} focus to the result, accepts confrontation as part of the path, and may dismiss objections {$HE} sees as unhelpful. The risk: {$HE} can run past colleagues who need more context, time, or buy-in.",
            4  => "Under pressure, {$f} reaches for connection. {$HHE} talks more, looks for allies, and uses energy and optimism to keep momentum. The risk: {$HE} can over-promise to keep the mood positive, or substitute reassurance for the harder structural fix.",
            2  => "Under pressure, {$f} slows down and steadies the ground. {$HHE} works one assignment at a time, resists sudden change, and projects calm. The risk: outside parties may read {$HIS} composure as resistance, and urgent shifts can stall waiting for {$HIM} to absorb them.",
            1  => "Under pressure, {$f} returns to evidence and process. {$HHE} re-checks {$HIS} work, asks for clarification, and resists committing until {$HE} is sure. The risk: in time-pressured situations, the search for certainty can read as hesitation.",
            12 => "Under pressure, {$f} accelerates and asserts. {$HHE} takes the floor, pushes for action, and uses social energy to drive a result. The risk: speed and confidence can outpace the quieter information {$HE} would benefit from hearing.",
            10 => "Under pressure, {$f} digs in. {$HHE} balances ambition with a strong preference for stable ground, which can produce determined, persistent effort — but also visible tension when the situation forces change.",
            9  => "Under pressure, {$f} becomes more exacting. {$HHE} drives toward the right answer and may grow critical of work that falls short. The risk: others can feel scrutinized rather than supported.",
            6  => "Under pressure, {$f} seeks people and consensus. {$HHE} prefers to talk a problem through and avoid hard confrontation. The risk: relational care can delay decisions that need to be made directly.",
            5  => "Under pressure, {$f} pulls between two instincts — to engage and energize others, and to defer to rules and structure. The result is often a measured, diplomatic response that may take longer than peers expect.",
            3  => "Under pressure, {$f} becomes more deliberate. {$HHE} resists premature decisions, leans on established procedure, and waits for clarity. The risk: in fast-moving situations, deliberation can be misread as disengagement.",
            14 => "Under pressure, {$f} doubles down on independence. {$HHE} takes ownership, sets {$HIS} own pace, and may decline help. The risk: colleagues may not realize how much {$HE} is carrying.",
            13 => "Under pressure, {$f} moves quickly and visibly. {$HHE} thinks on {$HIS} feet, adapts to new information, and engages others persuasively. The risk: pace can outrun the quieter analytical work that would strengthen the decision.",
            11 => "Under pressure, {$f} relies on facts, structure, and persistence. {$HHE} prefers to work the problem rather than the people. The risk: collaborators may feel out of the loop on the reasoning.",
            7  => "Under pressure, {$f} works to keep relationships intact and the team moving. {$HHE} listens, supports, and avoids unnecessary friction. The risk: difficult truths can go unsaid longer than they should.",
            default => "Under pressure, {$f}'s response reflects a balanced profile — adapting fluidly to the situation rather than defaulting to a single fixed reaction.",
        };
    }

    /** S-15 Conflict Style — driven by the dominant Mask dimension. */
    private function conflictStyle(string $dim, string $f, array $g): string
    {
        ['HHE' => $HHE, 'HE' => $HE, 'HIS' => $HIS, 'HIM' => $HIM, 'HHIS' => $HHIS] = $g;
        return match ($dim) {
            'D' => "In conflict, {$f} engages directly. {$HHE} states {$HIS} position, expects others to do the same, and treats disagreement as part of getting to the right answer rather than a personal breach. Resolution comes faster when the other party matches {$HIS} directness; it stalls when they retreat.",
            'I' => "In conflict, {$f} reaches for dialogue and persuasion. {$HHE} prefers to talk the issue out, often working to restore the relational tone before tackling the substance. {$HHIS} optimism can defuse tension — and can also gloss over the harder structural disagreement.",
            'S' => "In conflict, {$f} steps back and looks for common ground. {$HHE} dislikes open confrontation and prefers private, measured conversations to public disputes. Resolution is more likely when others approach {$HIM} calmly and give {$HIM} time to consider {$HIS} response.",
            'C' => "In conflict, {$f} works the facts. {$HHE} prefers to clarify the data, the rule, or the precedent before debating the interpretation. {$HHE} avoids emotional escalation and may withdraw if a discussion turns heated rather than analytical.",
            default => "{$f}'s approach to conflict draws on multiple DISC dimensions and adapts to the situation.",
        };
    }

    /** S-16 How Others Perceive [Name] — uses the Mask/Latent gap. */
    private function othersPerception(array $mp, array $lp, string $f, array $g): string
    {
        ['HHE' => $HHE, 'HE' => $HE, 'HIS' => $HIS, 'HIM' => $HIM, 'HHIS' => $HHIS] = $g;

        $maxGap = 0;
        $gapDim = 0;
        for ($i = 0; $i < 4; $i++) {
            $gap = abs($lp[$i] - $mp[$i]);
            if ($gap > $maxGap) {
                $maxGap = $gap;
                $gapDim = $i;
            }
        }

        if ($maxGap < 15) {
            return "Others tend to experience {$f} consistently across settings. {$HHIS} adapted behavior closely tracks {$HIS} natural style, which makes {$HIM} feel predictable and authentic. The benefit is trust; the risk is that {$HE} may have fewer registers to draw on in situations that call for a different mode.";
        }

        $label = ['Dominance', 'Influence', 'Steadiness', 'Conscientiousness'][$gapDim];
        $direction = $lp[$gapDim] > $mp[$gapDim]
            ? "express more {$label} privately than {$HE} shows in public settings"
            : "show more {$label} in public settings than {$HE} prefers to express privately";

        return "Others see a public-facing version of {$f} that does not fully match {$HIS} natural style. Specifically, {$HE} tends to {$direction}. Over time this gap costs energy to maintain, and the people closest to {$f} may notice the difference between how {$HE} performs and how {$HE} actually feels.";
    }

    private function workingWith(string $dim, string $f): string
    {
        return match ($dim) {
            'D' => "Present your message in a clear, brief, pointed manner and do not ramble or waste time with {$f}. Stick to business rather than trying to build a personal relationship. Prepare materials such as requirements, objectives and support material in a well organized package. Ask specific questions (preferably 'What?') rather than rhetorical ones. Provide facts and figures about the probability of success or effectiveness of options. When you disagree about something, take issue with facts, rather than the person. Provide choices and alternatives for decision making — ready-made decisions may cause unnecessary friction.",
            'I' => "Plan interactions that support {$f}'s dreams and intentions. Leave time for socializing and personal relations — {$f} prefers not to work with curt, cold, and tight-lipped people. People and their goals are more stimulating to {$f} than facts, figures, and abstractions. Ask {$f} for opinions and ideas regarding people. Focus your energy on ideas for implementing action. Motivation may be enhanced by testimonials from people {$f} sees as important or prominent. Offer special incentives for risk-taking and commitment to action.",
            'S' => "Prepare your case in advance. Do not present disorganized or messy proposals for action. Approach {$f} in a straightforward, direct manner. Support {$f}'s principles and build your credibility by listing pros and cons to suggestions you make. Don't rush the decision-making process. Be specific and do what you say you can do. Schedule implementation with a step-by-step timetable. Assure {$f} that there will not be surprises. Give time to verify the reliability of your actions.",
            'C' => "Start conversations with a personal comment — don't rush headlong into business or the agenda. Show interest in {$f} as a person and find areas of common involvement. Present your case softly, and in a nonthreatening manner. Ask 'how' questions to draw out {$f}'s opinions. Do not manipulate or bully {$f} into agreeing. Provide guarantees that {$f}'s decision will minimize risks. Offer options and probabilities rather than vague assurances. Provide personal assurances and clear, specific solutions with maximum guarantees.",
            default => "Adapt your communication style to work effectively with {$f}.",
        };
    }
}
