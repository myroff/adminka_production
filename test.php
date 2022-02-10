<?php
#clearstatcache();
#phpinfo();


class Position
{
    public int $rowIndex;
    public int $columnIndex;
    function __construct(int $rowIndex, int $columnIndex)
    {
        $this->rowIndex = $rowIndex;
        $this->columnIndex = $columnIndex;
    }

    function leftDiagonal(): int {
        return $this->rowIndex - $this->columnIndex;
    }

    function rightDiagonal(): int {
        return $this->rowIndex + $this->columnIndex;
    }

    function __toString(): string {
        return sprintf('Position (%d, %d)', $this->rowIndex, $this->columnIndex);
    }
}

class QueensProblem {
    static function isSafeRook(array $positions, $rook) : bool {
        $out = true;

        foreach($positions as $pos){
            if($pos->rowIndex === $rook->rowIndex
                || $pos->columnIndex === $rook->columnIndex)
                {
                    $out = false;
                    break;
                }
        }
        return $out;
    }

    static function isSafeQueen(array $positions, $queen) : bool {
        $out = true;

        foreach($positions as $pos){
            if($pos->rowIndex === $queen->rowIndex
                || $pos->columnIndex === $queen->columnIndex
                || $pos->leftDiagonal() === $queen->leftDiagonal()
                || $pos->rightDiagonal() === $queen->rightDiagonal()
            )
            {
                $out = false;
                break;
            }
        }
        return $out;
    }

    static function getQueensProblemSolution(int $boardSize) : array {
        if($boardSize<4){
            return [];
        }
        $reults = QueensProblem::getTree(0, $boardSize, $boardSize, []);
        $hits = QueensProblem::getMaxResults($reults);
        return $hits[0];
    }
    
    static public function getVariantsInRow(int $rowNr, int $width, array $positions) {
        
        $out = [];
        
        for($col=0; $col < $width; $col++) {
            
            $_t = new Position($rowNr, $col);
            
            if(self::isSafeQueen($positions, $_t)) {
                $out[] = $_t;
            }
        }
        
        return $out;
    }

    static public function getTree($currentRow, $height, $width, $positionsVariants) {

        $out = [];
        $variants = [];

        if($currentRow < $height){
            if(empty($positionsVariants)){
                $firstVariants = self::getVariantsInRow($currentRow, $width, []);
                foreach($firstVariants as $k=> $v){
                    $startArray = array();
                    $startArray[0] = $v;
                    $variants[$k] = $startArray;
                }
            }
            else{ 

                foreach($positionsVariants as $key => $positions){

                    $children = self::getVariantsInRow($currentRow, $width, $positions);

                    if(!empty($children)){
                        foreach($children as $child){
                            $_t = $positions;
                            $_t[] = $child;
    
                            $variants[] = $_t;
                        }
                    }else{
                        #$_t = $positions;
                        #$variants[] = $_t;
                    }
                }

            }

            $currentRow++;

            if(!empty($variants)){

                $out = self::getTree($currentRow, $height, $width, $variants);
            }
            return $out;
        }

        return $positionsVariants;
    }

    static public function getMaxResults(array $results) {
        $out = [];

        $maxLength = -1;

        foreach($results as $res){
            
            $curLength = count($res);

            if($curLength > $maxLength){
                $out = [];
                $out[] = $res;
                $maxLength = $curLength;
            }
            elseif($curLength === $maxLength){
                $out[] = $res;
            }
        }
        return $out;
    }
}
$results = QueensProblem::getQueensProblemSolution(100);
#$results = QueensProblem::getTree(0, 6, 6, []);
#$results = QueensProblem::getMaxResults($test);

echo "\n<br>function result: ".count($results);
echo "<pre>";
print_r($results);
echo "</pre>";