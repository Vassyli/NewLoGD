<?php
/**
 * NewLoGD
 *
 * @author      Basilius Sauter <basilius.sauter@hispeed.ch>
 * @copyright   Copyright (c) 2015, Basilius Sauter
 * @licence     https://www.gnu.org/licenses/agpl-3.0.html GNU Affero GPL 3.0
 */

class TableGenerator extends Datatypes {
	protected $cols = array();
	protected $rows = array();
	
	public function __construct() {
		
	}
	
	public function getHtml() {
		$buffer = "<table>\n";
        $buffer.= "\t<thead><tr>\n";
        // Table-Head
        foreach($this->cols as $col) {
            $title = htmlspecialchars($col["title"]);
            $buffer.= "\t\t<th>{$title}</th>\n";
        }
        $buffer.= "\t</tr></thead>\n";
        $buffer.= "\t<tbody><tr>\n";
        // Table-Body
        $i = 0;
        foreach($this->rows as $row) {
            if($i > 0) { $buffer .= "\t</tr><tr>"; }
            if(is_array($row)) {
                foreach($this->cols as $id => $col) {
                    $content = htmlspecialchars($row[$id]);
                    $buffer .= "\t\t<td>{$content}</td>";
                }
            }
            elseif($row instanceof \Modelitem) {
                foreach($this->cols as $id => $col) {
                    $field = filter_var($id, FILTER_CALLBACK, array("options" => "filter_word"));
                    $methodname = "get".$field;
                    if(!method_exists($row, $methodname)) {
                        // Support of oldstyle method names (Deprecated)
                        $methodname = "get_".$field;
                    }
                    
                    $content = htmlspecialchars($row->$methodname());
                    // Use later the property of $col to check fieldtype
                    if(mb_strlen($content) > 255) {
                        $content = mb_substr($content, 0, 255)."[...]";
                    }
                    $buffer .= "\t\t<td>{$content}</td>";
                }
            }
            else {
                $buffer .= "<td>NO IDEA</td>";
            }
            $i++;
        }
        $buffer.= "\t</tr></tbody>\n";
        $buffer.= "</table>";
        return $buffer;
	}
	
	public function addCol($col_id, $col_title) {
        $this->cols[$col_id] = array(
            "title" => $col_title,
        );
		return $this;
	}
    
    public function addCols(array $cols) {
        foreach($cols as $id => $title) {
            $this->addCol($id, $title);
        }
        return $this;
    }
	
	public function addRow($row) {
        array_push($this->rows, $row);
		return $this;
	}
    
    public function addRows($rows) {
        $this->rows = array_merge($this->rows, $rows);
    }
}