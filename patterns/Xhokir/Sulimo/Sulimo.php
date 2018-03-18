<?php
/** Freesewing\Patterns\Templates\PatternTemplate class */
namespace Freesewing\Patterns\Xhokir;

use Freesewing\Utils;
use Freesewing\BezierToolbox;

/**
 * A pattern template
 *
 * If you'd like to add you own pattern, you can copy this class/directory.
 * It's an empty skeleton for you to start working with
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2017 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class Sulimo extends \Freesewing\Patterns\Core\Pattern
{
    /*
        ___       _ _   _       _ _
       |_ _|_ __ (_) |_(_) __ _| (_)___  ___
        | || '_ \| | __| |/ _` | | / __|/ _ \
        | || | | | | |_| | (_| | | \__ \  __/
       |___|_| |_|_|\__|_|\__,_|_|_|___/\___|

      Things we need to do before we can draft a pattern
    */

    /**
     * Sets up options and values for our draft
     *
     * By branching this out of the sample/draft methods, we can
     * set a bunch of options and values the influence the draft
     * without having to touch the sample/draft methods
     * When extending this pattern so we can just implement the
     * initialize() method and re-use the other methods.
     *
     * Good to know:
     * Options are typically provided by the user, but sometimes they are fixed
     * Values are calculated for re-use later
     *
     * @param \Freesewing\Model $model The model to sample for
     *
     * @return void
     */
    public function initialize($model)
    {

        // Set values for use later
        $this->setOption('sa', 10);
        //$this->setOption('savePaper',2);
        $this->setValue('flyWidth', 40);
        $this->setvalue('crotchRadius',60);

    }

    /*
        ____             __ _
       |  _ \ _ __ __ _ / _| |_
       | | | | '__/ _` | |_| __|
       | |_| | | | (_| |  _| |_
       |____/|_|  \__,_|_|  \__|

      The actual sampling/drafting of the pattern
    */

    /**
     * Generates a sample of the pattern
     *
     * Here, you create a sample of the pattern for a given model
     * and set of options. You should get a barebones pattern with only
     * what it takes to illustrate the effect of changes in
     * the sampled option or measurement.
     *
     * @param \Freesewing\Model $model The model to sample for
     *
     * @return void
     */
    public function sample($model)
    {
      // Setup all options and values we need
      $this->initialize($model);

      // Draft our back part
      $this->draftBackPart($model);
      if($this->o('savePaper')==1){
        $this->draftBeltPart($model);
      }
    }

    /**
     * Generates a draft of the pattern
     *
     * Here, you create the full draft of this pattern for a given model
     * and set of options. You get a complete pattern with
     * all bels and whistles.
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draft($model)
    {
      // Continue from sample
      $this->sample($model);

      // Finalize our back part
      if($this->o('savePaper')==1){
        $this->finalizeBeltPart($model);
      }
      $this->finalizeBackPart($model);

      // Is this a paperless pattern?
      if ($this->isPaperless) {
        // Add paperless info to our back part
        $this->paperlessBackPart($model);
      }
    }

    public function draftBeltPart($model)
    {
      /** @var \Freesewing\Part $p */
      $p = $this->parts['beltPart'];

      // Without seam allowance
      $p->newPoint('belt1',0,0,"Top-left of the belt part");
      $p->newPoint('belt2',
                  $p->x('belt1')+$model->m('hipsCircumference')/2+$this->o('hipsEase')/2,
                  $p->y('belt1'),
                  "Top-right of the belt part");
      $p->newPoint('belt3',
                  $p->x('belt2'),
                  $p->y('belt2')+2*$this->o('elasticWidth'),
                  "Bottom-right of the belt part");
      $p->newPoint('belt4',
                  $p->x('belt1'),
                  $p->y('belt3'),
                  "Bottom-left of the belt part");

      $p->newPath('beltPart','M belt1 L belt2 L belt3 L belt4 L belt1');
      $p->paths['beltPart']->setSample(true);
    }

    public function draftBackPart($model)
    {

        // FRONT
        $pf = $this->parts['frontPart'];

        $pf->newPoint('front1',0,0,"Top-left of the front part");
        $pf->newPoint('front2',
                    $pf->x('front1'),
                    $pf->y('front1')+$model->m('hipsToUpperLeg')+$this->o('legBonus'));
        $pf->newPoint('front3',
                    $pf->x('front2')+$model->m('upperLegCircumference')/2+$this->o('legEase')/2,
                    $pf->y('front2'));
        $pf->newPoint('front5',
                    $pf->x('front1')+$model->m('hipsCircumference')/4+$this->o('hipsEase')/4,
                    $pf->y('front1')+$model->m('hipsToUpperLeg'));
        $pf->newPoint('front4',
                    $pf->x('front5')+$this->v('crotchRadius'),
                    $pf->y('front5')+$this->v('crotchRadius'));
        $pf->newPoint('front6',
                    $pf->x('front5'),
                    $pf->y('front1'));

        // fly
        $pf->newPoint('frontfly1',
                      $pf->x('front5')+$this->v('flyWidth'),
                      $pf->y('front5'));
        $pf->newPoint('frontfly2',
                      $pf->x('front6')+$this->v('flyWidth'),
                      $pf->y('front6'));

        // control points
        $pf->newPoint('frontControl4',
                    .5*$this->v('crotchRadius')+$anchorX+$model->m('hipsCircumference')/4+$this->o('hipsEase')/4,
                    $this->v('crotchRadius')+$anchorY+$model->m('hipsToUpperLeg'));
        $pf->newPoint('frontControl5',
                    $model->m('hipsCircumference')/4+$this->o('hipsEase')/4,
                    .5*$this->v('crotchRadius')+$model->m('hipsToUpperLeg'));

        // path
        $pf->newPath('frontPart', 'M front1 L front2 L front3 L front4 C frontControl4 frontControl5 front5 L frontfly1 L frontfly2 L front6 L front1');
        $pf->paths['frontPart']->setSample(true);

        // additional path for paper saving option
        if($this->o('savePaper')==2){
          $pf->newPath('savePaperPath', 'M front5 L front6');

          $pf->newPoint('frontBelt1', $pf->x('front1'), $pf->y('front2')-2*$this->o('elasticWidth'));
          $pf->newPoint('frontBelt2', $pf->x('front6'), $pf->y('front2')-2*$this->o('elasticWidth'));
          $pf->newPoint('frontBelt3', $pf->x('front6'), $pf->y('front2'));
          $pf->newPoint('frontBelt4', $pf->x('front1'), $pf->y('front2'));

          $pf->newPath('beltPathSavePaper','M frontBelt1 L frontBelt2 L frontBelt3 L frontBelt4 L frontBelt1', ['class' => 'various fabric']);
        }


        // BACK
        if($this->o('savePaper')==1){
          $p = $this->parts['backPart'];
          $p->addPoint('back1',$pf->shift('front1',0,0));
          $p->addPoint('back2',$pf->shift('front2',0,0));
          $p->addPoint('back3',$pf->shift('front3',0,0));
          $p->addPoint('back4',$pf->shift('front4',0,0));
          $p->addPoint('back5',$pf->shift('front5',0,0));
          $p->addPoint('back6',$pf->shift('front6',0,0));

          $p->addPoint('backControl4',$pf->shift('frontControl4',0,0));
          $p->addPoint('backControl5',$pf->shift('frontControl5',0,0));

          $p->newPath('backPart', 'M back1 L back2 L back3 L back4 C backControl4 backControl5 back5 L back6 L back1');
          $p->paths['backPart']->setSample(true);
      }


    }

    /*
       _____ _             _ _
      |  ___(_)_ __   __ _| (_)_______
      | |_  | | '_ \ / _` | | |_  / _ \
      |  _| | | | | | (_| | | |/ /  __/
      |_|   |_|_| |_|\__,_|_|_/___\___|

      Adding titles/logos/seam-allowance/grainline and so on
    */

    public function finalizeBeltPart($model)
    {
      /** @var \Freesewing\Part $p */
      $p = $this->parts['beltPart'];

      // SEAM ALLOWANCE
      $p->offsetPathString('saBelt0','M belt1 L belt2',$this->o('sa')*2,true,['class' => 'fabric sa']);
      $p->offsetPathString('saBelt1','M belt2 L belt3',$this->o('sa'),true,['class' => 'fabric sa']);
      $p->newPath('saBeltJoin1','M saBelt0-endPoint L saBelt1-startPoint',['class' => 'fabric sa']);
      $p->offsetPathString('saBelt2','M belt3 L belt4 ',2*$this->o('sa'),true,['class' => 'fabric sa']);
      $p->newPath('saBeltJoin','M saBelt1-endPoint L saBelt2-startPoint M saBelt2-endPoint L saBelt0-startPoint',['class' => 'fabric sa']);

      // Cut on fold
      //$p->newPoint('cutAnchor',$p->x("belt1"),($p->y('belt1')+$p->y('belt4'))/2);
      //$p->newNote('curFold','cutAnchor','Cut on fold');
      $p->newPoint('cutFold1',$p->x('belt1'),$p->y('belt1')+5);
      $p->newPoint('cutFold2',$p->x('belt4'),$p->y('belt4')-5);
      $p->newCutonfold('cutFold1','cutFold2',"Cut on fold",-20);


      // TITLE
      $p->newPoint('titleAnchorBelt',
                    ($p->x('belt1')+$p->x('belt2'))/2,
                    ($p->y('belt1')+$p->y('belt4'))/2);
      $p->addTitle('titleAnchorBelt', '3/3', $this->t($p->getTitle()), 'Cut on fold');
    }

    public function finalizeBackPart($model)
    {
        // FRONT
        // Seam allowance
        $pf = $this->parts['frontPart'];
        $pf->offsetPathString('saFront1','M front1 L front2',$this->o('sa')*-1,true,['class' => 'fabric sa']);
        $pf->offsetPathString('saFront2','M front2 L front3',$this->o('sa')*-2,true,['class' => 'fabric sa']);
        $pf->offsetPathString('saFront3','M front3 L front4 C frontControl4 frontControl5 front5 L frontfly1',$this->o('sa')*-1,true,['class' => 'fabric sa']);
        $pf->offsetPathString('saFront4','M frontfly1 L frontfly2',$this->o('sa')*-2,true,['class' => 'fabric sa']);

        $pf->newPath('saFrontJoin','M saFront1-endPoint L saFront2-startPoint
                                    M saFront2-endPoint L saFront3-startPoint
                                    M saFront3-endPoint L saFront4-startPoint
                                    M saFront4-endPoint L saFront1-startPoint',['class' => 'fabric sa']);

        // Button
        $pf->newPoint('buttonAnchor',
                      ($pf->x('front6')+$pf->x('frontfly2'))/2,
                      ($pf->y('frontfly1')+$pf->y('frontfly2'))/2);
        $pf->newSnippet('buttonAnchor','buttonhole','buttonAnchor');

        // Title
        $pf->newPoint('titleAnchorFront',
                      ($pf->x('front1')+$pf->x('front6'))/2,
                      ($pf->y('front1')+$pf->y('front2'))/2.5);

        if($this->o('savePaper')==1){
          $pf->addTitle('titleAnchorFront', '1/3', $this->t($pf->getTitle()), 'Cut 2');
        }else{
          $pf->addTitle('titleAnchorFront', '1/1', 'All in 1', 'Cut 4, but only 2 with the fly, plus the belt');
        }

        // additional path for paper saving option
        if($this->o('savePaper')==2){
          $pf->offsetPathString('beltsaSavePaper1','M frontBelt1 L frontBelt2 L frontBelt3', $this->o('sa')*1,true,['class' => 'various sa']);
          $pf->offsetPathString('beltsaSavePaper2','M frontBelt3 L frontBelt4', $this->o('sa')*2,true,['class' => 'various sa']);
          $pf->newPath('beltsaSavePaperLink','M beltsaSavePaper1-endPoint L beltsaSavePaper2-startPoint',['class' => 'various sa']);
          $pf->newPath('beltsaSavePaperLink2','M beltsaSavePaper2-endPoint L beltsaSavePaper1-startPoint',['class' => 'various sa']);

          $pf->offsetPathString('sasavePaper','M front5 L front6', $this->o('sa')*-1,true,['class' => 'various sa']);

          $pf->newPoint('cutFold1',$pf->x('front6')+$this->o('sa'),$pf->y('front6')+10);
          $pf->newPoint('cutFold2',$pf->x('front5')+$this->o('sa'),$pf->y('front5')-10);
          $pf->newCutonfold('cutFold1','cutFold2',"Fold here to cut back part",+20);

          $pf->newPoint('cutTwice1',$pf->x('front1'),($pf->y('frontBelt1')+$pf->y('frontBelt4'))/2);
          $pf->newPoint('cutTwice2',$pf->x('front6')+$this->o('sa'),($pf->y('frontBelt1')+$pf->y('frontBelt4'))/2);
          $pf->newCutonfold('cutTwice1','cutTwice2',"To cut the belt, cut twice the length on fold",0);
        }


        // BACK
        if($this->o('savePaper')==1){
          $p = $this->parts['backPart'];

          $p->offsetPathString('saBack1','M back1 L back2',$this->o('sa')*-1,true,['class' => 'fabric sa']);
          $p->offsetPathString('saBack2','M back2 L back3',$this->o('sa')*-2,true,['class' => 'fabric sa']);
          $p->offsetPathString('saBack3','M back3 L back4 C backControl4 backControl5 back5 L back6',$this->o('sa')*-1,true,['class' => 'fabric sa']);
          $p->newPath('saBackJoin','M saBack1-endPoint L saBack2-startPoint M saBack2-endPoint L saBack3-startPoint M saBack3-endPoint L saBack1-startPoint',['class' => 'fabric sa']);
          /*
          $p->offsetPathString('saBack1','M back2 L back1 L back6 L back5 C backControl5 backControl4 back4 L back3',$this->o('sa'),true,['class' => 'fabric sa']);
          $p->offsetPathString('saBack2','M back2 L back3',$this->o('sa')*-2,true,['class' => 'fabric sa']);
          $p->newPath('saBackJoin','M saBack1-startPoint L saBack2-startPoint M saBack2-endPoint L saBack1-endPoint',['class' => 'fabric sa']);
          */
          $p->newPoint('titleAnchorBack',
                        ($p->x('back1')+$p->x('back6'))/2,
                        ($p->y('back1')+$p->y('back2'))/4);
          $p->addTitle('titleAnchorBack', '2/3', $this->t($p->getTitle()), 'Cut 2');
      }
    }

    /*
        ____                       _
       |  _ \ __ _ _ __   ___ _ __| | ___  ___ ___
       | |_) / _` | '_ \ / _ \ '__| |/ _ \/ __/ __|
       |  __/ (_| | |_) |  __/ |  | |  __/\__ \__ \
       |_|   \__,_| .__/ \___|_|  |_|\___||___/___/
                  |_|

      Instructions for paperless patterns
    */

    /**
     * Adds paperless info for the back part
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function paperlessBackPart($model)
    {
        /** @var \Freesewing\Part $p */
                // FRONT
        $pf = $this->parts['frontPart'];
        $pf->newWidthDimension('front1','frontfly1',$pf->y('front1')+50);
        $pf->newWidthDimension('front2','front3',$pf->y('front2')-10);
        $pf->newWidthDimension('front5','front4',$pf->y('front4'));
        $pf->newWidthDimension('front5','frontfly2',$pf->y('front5')-10);

        $pf->newHeightDimension('front1','front2',$pf->x('front1')+10);
        $pf->newHeightDimension('front5','front4',$pf->x('front5'));
        $pf->newHeightDimension('frontfly1','frontfly2',$pf->x('frontfly1')+15);

        // additional path for paper saving option
        if($this->o('savePaper')==2){
          $pf->newWidthDimension('front1','front6',$pf->x('front1')+30);
        }


        // BACK
      if($this->o('savePaper')==1){
        $p = $this->parts['backPart'];

        $p->newWidthDimension('back1','back6',$p->y('back1')+10);
        $p->newWidthDimension('back2','back3',$p->y('back2')-10);
        $p->newWidthDimension('back5','back4',$p->y('back5'));

        $p->newHeightDimension('back1','back2',$p->x('back1')+10);
        $p->newHeightDimension('back5','back4',$p->x('back5'));


        //BELT
        $pb = $this->parts['beltPart'];
        $pb->newWidthDimension('belt1','belt2',$pb->y('belt1')-15);
        $pb->newHeightDimension('belt1','belt4',$pb->x('belt1')+15);
      }
    }
}
