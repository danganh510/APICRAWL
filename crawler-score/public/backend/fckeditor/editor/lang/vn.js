/*
 * FCKeditor - The text editor for Internet - http://www.fckeditor.net
 * Copyright (C) 2003-2007 Frederico Caldeira Knabben
 *
 * == BEGIN LICENSE ==
 *
 * Licensed under the terms of any of the following licenses at your
 * choice:
 *
 *  - GNU General Public License Version 2 or later (the "GPL")
 *    http://www.gnu.org/licenses/gpl.html
 *
 *  - GNU Lesser General Public License Version 2.1 or later (the "LGPL")
 *    http://www.gnu.org/licenses/lgpl.html
 *
 *  - Mozilla Public License Version 1.1 or later (the "MPL")
 *    http://www.mozilla.org/MPL/MPL-1.1.html
 *
 * == END LICENSE ==
 *
 * English language file.
 */

var FCKLang =
{
// Language direction : "ltr" (left to right) or "rtl" (right to left).
Dir					: "ltr",

ToolbarCollapse		: "Collapse Toolbar",
ToolbarExpand		: "Expand Toolbar",

// Toolbar Items and Context Menu
Save				: "Lưu",
NewPage				: "Trang mới",
Preview				: "Xem thử",
Cut					: "Cắt",
Copy				: "Sao chép",
Paste				: "Dán",
PasteText			: "Dán như text phẳng",
PasteWord			: "Dán từ Word",
Print				: "In",
SelectAll			: "Chõn tất cả",
RemoveFormat		: "Bỏ định dạng",
InsertLinkLbl		: "Liên kết",
InsertLink			: "Chèn/Sửa liên kết",
RemoveLink			: "Bỏ liên kết",
Anchor				: "Chèn/Sửa Anchor",
AnchorDelete		: "Bỏ Anchor",
InsertImageLbl		: "Hình",
InsertImage			: "Chèn/Sửa hình",
InsertFlashLbl		: "Flash",
InsertFlash			: "Chèn/Sửa Flash",
InsertTableLbl		: "Bảng",
InsertTable			: "Chèn/Sửa bảng",
InsertLineLbl		: "Dòng kẻ",
InsertLine			: "Chèn dòng kẻ ngang",
InsertSpecialCharLbl: "Ký tự dặt biệt",
InsertSpecialChar	: "Thêm ký tự dặt biệt",
InsertSmileyLbl		: "Hình cảm xúc",
InsertSmiley		: "Chèn hình cảm xúc",
About				: "Về FCKeditor",
Bold				: "Đậm",
Italic				: "Nghiêng",
Underline			: "Gạch chân",
StrikeThrough		: "Gạch giữa",
Subscript			: "Subscript",
Superscript			: "Superscript",
LeftJustify			: "Canh trái",
CenterJustify		: "Canh giữa",
RightJustify		: "Canh phải",
BlockJustify		: "Canh điều",
DecreaseIndent		: "Thụt đầu dòng",
IncreaseIndent		: "Không thụt đầu dòng",
Blockquote			: "Ghi chú",
Undo				: "Về trước",
Redo				: "Về sau",
NumberedListLbl		: "Danh sách số",
NumberedList		: "Thêm/xóa danh sách số",
BulletedListLbl		: "Dấu đầu dòng",
BulletedList		: "Thêm/xóa dấu đầu dòng",
ShowTableBorders	: "Nhìn thấy border",
ShowDetails			: "Nhìn chi tiết",
Style				: "Kiểu",
FontFormat			: "Định dạng",
Font				: "Font",
FontSize			: "Cở chữ",
TextColor			: "Màu chữ",
BGColor				: "Màu nền",
Source				: "Mã htnl",
Find				: "Tìm",
Replace				: "Thay thế",
SpellCheck			: "Kiểm tra lỗi",
UniversalKeyboard	: "Phím nóng",
PageBreakLbl		: "Gắt trang",
PageBreak			: "Chèn ngắt trang",

Form			: "Form",
Checkbox		: "Checkbox",
RadioButton		: "Radio Button",
TextField		: "Text Field",
Textarea		: "Textarea",
HiddenField		: "Hidden Field",
Button			: "Nút",
SelectionField	: "Selection Field",
ImageButton		: "Nút có hình",

FitWindow		: "Mở rộng tối đa",
ShowBlocks		: "Xem khối",

// Context Menu
EditLink			: "sữa liên kết",
CellCM				: "Ô",
RowCM				: "Dòng",
ColumnCM			: "Cột",
InsertRowAfter		: "Thêm dòng xuống sau cùng",
InsertRowBefore		: "Thêm dòng lên trên cùng",
DeleteRows			: "Xóa dòng",
InsertColumnAfter	: "Thêm cột sau cùng",
InsertColumnBefore	: "Thêm cột ra trứơc",
DeleteColumns		: "Xóa cột",
InsertCellAfter		: "Thêm ô sau cùng",
InsertCellBefore	: "Them ô ra trứơc",
DeleteCells			: "Xóa ô",
MergeCells			: "Dồn ô",
MergeRight			: "Dồn ô qua phải",
MergeDown			: "Dồn ô lên trên",
HorizontalSplitCell	: "Split Cell Horizontally",
VerticalSplitCell	: "Split Cell Vertically",
TableDelete			: "Delete Table",
CellProperties		: "Cell Properties",
TableProperties		: "Table Properties",
ImageProperties		: "Image Properties",
FlashProperties		: "Flash Properties",

AnchorProp			: "Anchor Properties",
ButtonProp			: "Button Properties",
CheckboxProp		: "Checkbox Properties",
HiddenFieldProp		: "Hidden Field Properties",
RadioButtonProp		: "Radio Button Properties",
ImageButtonProp		: "Image Button Properties",
TextFieldProp		: "Text Field Properties",
SelectionFieldProp	: "Selection Field Properties",
TextareaProp		: "Textarea Properties",
FormProp			: "Form Properties",

FontFormats			: "Normal;Formatted;Address;Heading 1;Heading 2;Heading 3;Heading 4;Heading 5;Heading 6;Normal (DIV)",

// Alerts and Messages
ProcessingXHTML		: "Processing XHTML. Please wait...",
Done				: "Done",
PasteWordConfirm	: "The text you want to paste seems to be copied from Word. Do you want to clean it before pasting?",
NotCompatiblePaste	: "This command is available for Internet Explorer version 5.5 or more. Do you want to paste without cleaning?",
UnknownToolbarItem	: "Unknown toolbar item \"%1\"",
UnknownCommand		: "Unknown command name \"%1\"",
NotImplemented		: "Command not implemented",
UnknownToolbarSet	: "Toolbar set \"%1\" doesn't exist",
NoActiveX			: "Your browser's security settings could limit some features of the editor. You must enable the option \"Run ActiveX controls and plug-ins\". You may experience errors and notice missing features.",
BrowseServerBlocked : "The resources browser could not be opened. Make sure that all popup blockers are disabled.",
DialogBlocked		: "It was not possible to open the dialog window. Make sure all popup blockers are disabled.",

// Dialogs
DlgBtnOK			: "OK",
DlgBtnCancel		: "Cancel",
DlgBtnClose			: "Close",
DlgBtnBrowseServer	: "Browse Server",
DlgAdvancedTag		: "Advanced",
DlgOpOther			: "<Other>",
DlgInfoTab			: "Info",
DlgAlertUrl			: "Please insert the URL",

// General Dialogs Labels
DlgGenNotSet		: "<not set>",
DlgGenId			: "Id",
DlgGenLangDir		: "Language Direction",
DlgGenLangDirLtr	: "Left to Right (LTR)",
DlgGenLangDirRtl	: "Right to Left (RTL)",
DlgGenLangCode		: "Language Code",
DlgGenAccessKey		: "Access Key",
DlgGenName			: "Name",
DlgGenTabIndex		: "Tab Index",
DlgGenLongDescr		: "Long Description URL",
DlgGenClass			: "Stylesheet Classes",
DlgGenTitle			: "Advisory Title",
DlgGenContType		: "Advisory Content Type",
DlgGenLinkCharset	: "Linked Resource Charset",
DlgGenStyle			: "Style",

// Image Dialog
DlgImgTitle			: "Image Properties",
DlgImgInfoTab		: "Image Info",
DlgImgBtnUpload		: "Send it to the Server",
DlgImgURL			: "URL",
DlgImgUpload		: "Upload",
DlgImgAlt			: "Alternative Text",
DlgImgWidth			: "Width",
DlgImgHeight		: "Height",
DlgImgLockRatio		: "Lock Ratio",
DlgBtnResetSize		: "Reset Size",
DlgImgBorder		: "Border",
DlgImgHSpace		: "HSpace",
DlgImgVSpace		: "VSpace",
DlgImgAlign			: "Align",
DlgImgAlignLeft		: "Left",
DlgImgAlignAbsBottom: "Abs Bottom",
DlgImgAlignAbsMiddle: "Abs Middle",
DlgImgAlignBaseline	: "Baseline",
DlgImgAlignBottom	: "Bottom",
DlgImgAlignMiddle	: "Middle",
DlgImgAlignRight	: "Right",
DlgImgAlignTextTop	: "Text Top",
DlgImgAlignTop		: "Top",
DlgImgPreview		: "Preview",
DlgImgAlertUrl		: "Please type the image URL",
DlgImgLinkTab		: "Link",

// Flash Dialog
DlgFlashTitle		: "Flash Properties",
DlgFlashChkPlay		: "Auto Play",
DlgFlashChkLoop		: "Loop",
DlgFlashChkMenu		: "Enable Flash Menu",
DlgFlashScale		: "Scale",
DlgFlashScaleAll	: "Show all",
DlgFlashScaleNoBorder	: "No Border",
DlgFlashScaleFit	: "Exact Fit",

// Link Dialog
DlgLnkWindowTitle	: "Link",
DlgLnkInfoTab		: "Link Info",
DlgLnkTargetTab		: "Target",

DlgLnkType			: "Link Type",
DlgLnkTypeURL		: "URL",
DlgLnkTypeAnchor	: "Link to anchor in the text",
DlgLnkTypeEMail		: "E-Mail",
DlgLnkProto			: "Protocol",
DlgLnkProtoOther	: "<other>",
DlgLnkURL			: "URL",
DlgLnkAnchorSel		: "Select an Anchor",
DlgLnkAnchorByName	: "By Anchor Name",
DlgLnkAnchorById	: "By Element Id",
DlgLnkNoAnchors		: "(No anchors available in the document)",
DlgLnkEMail			: "E-Mail Address",
DlgLnkEMailSubject	: "Message Subject",
DlgLnkEMailBody		: "Message Body",
DlgLnkUpload		: "Upload",
DlgLnkBtnUpload		: "Send it to the Server",

DlgLnkTarget		: "Target",
DlgLnkTargetFrame	: "<frame>",
DlgLnkTargetPopup	: "<popup window>",
DlgLnkTargetBlank	: "New Window (_blank)",
DlgLnkTargetParent	: "Parent Window (_parent)",
DlgLnkTargetSelf	: "Same Window (_self)",
DlgLnkTargetTop		: "Topmost Window (_top)",
DlgLnkTargetFrameName	: "Target Frame Name",
DlgLnkPopWinName	: "Popup Window Name",
DlgLnkPopWinFeat	: "Popup Window Features",
DlgLnkPopResize		: "Resizable",
DlgLnkPopLocation	: "Location Bar",
DlgLnkPopMenu		: "Menu Bar",
DlgLnkPopScroll		: "Scroll Bars",
DlgLnkPopStatus		: "Status Bar",
DlgLnkPopToolbar	: "Toolbar",
DlgLnkPopFullScrn	: "Full Screen (IE)",
DlgLnkPopDependent	: "Dependent (Netscape)",
DlgLnkPopWidth		: "Width",
DlgLnkPopHeight		: "Height",
DlgLnkPopLeft		: "Left Position",
DlgLnkPopTop		: "Top Position",

DlnLnkMsgNoUrl		: "Please type the link URL",
DlnLnkMsgNoEMail	: "Please type the e-mail address",
DlnLnkMsgNoAnchor	: "Please select an anchor",
DlnLnkMsgInvPopName	: "The popup name must begin with an alphabetic character and must not contain spaces",

// Color Dialog
DlgColorTitle		: "Select Color",
DlgColorBtnClear	: "Clear",
DlgColorHighlight	: "Highlight",
DlgColorSelected	: "Selected",

// Smiley Dialog
DlgSmileyTitle		: "Insert a Smiley",

// Special Character Dialog
DlgSpecialCharTitle	: "Select Special Character",

// Table Dialog
DlgTableTitle		: "Table Properties",
DlgTableRows		: "Rows",
DlgTableColumns		: "Columns",
DlgTableBorder		: "Border size",
DlgTableAlign		: "Alignment",
DlgTableAlignNotSet	: "<Not set>",
DlgTableAlignLeft	: "Left",
DlgTableAlignCenter	: "Center",
DlgTableAlignRight	: "Right",
DlgTableWidth		: "Width",
DlgTableWidthPx		: "pixels",
DlgTableWidthPc		: "percent",
DlgTableHeight		: "Height",
DlgTableCellSpace	: "Cell spacing",
DlgTableCellPad		: "Cell padding",
DlgTableCaption		: "Caption",
DlgTableSummary		: "Summary",

// Table Cell Dialog
DlgCellTitle		: "Cell Properties",
DlgCellWidth		: "Width",
DlgCellWidthPx		: "pixels",
DlgCellWidthPc		: "percent",
DlgCellHeight		: "Height",
DlgCellWordWrap		: "Word Wrap",
DlgCellWordWrapNotSet	: "<Not set>",
DlgCellWordWrapYes	: "Yes",
DlgCellWordWrapNo	: "No",
DlgCellHorAlign		: "Horizontal Alignment",
DlgCellHorAlignNotSet	: "<Not set>",
DlgCellHorAlignLeft	: "Left",
DlgCellHorAlignCenter	: "Center",
DlgCellHorAlignRight: "Right",
DlgCellVerAlign		: "Vertical Alignment",
DlgCellVerAlignNotSet	: "<Not set>",
DlgCellVerAlignTop	: "Top",
DlgCellVerAlignMiddle	: "Middle",
DlgCellVerAlignBottom	: "Bottom",
DlgCellVerAlignBaseline	: "Baseline",
DlgCellRowSpan		: "Rows Span",
DlgCellCollSpan		: "Columns Span",
DlgCellBackColor	: "Background Color",
DlgCellBorderColor	: "Border Color",
DlgCellBtnSelect	: "Select...",

// Find and Replace Dialog
DlgFindAndReplaceTitle	: "Find and Replace",

// Find Dialog
DlgFindTitle		: "Find",
DlgFindFindBtn		: "Find",
DlgFindNotFoundMsg	: "The specified text was not found.",

// Replace Dialog
DlgReplaceTitle			: "Replace",
DlgReplaceFindLbl		: "Find what:",
DlgReplaceReplaceLbl	: "Replace with:",
DlgReplaceCaseChk		: "Match case",
DlgReplaceReplaceBtn	: "Replace",
DlgReplaceReplAllBtn	: "Replace All",
DlgReplaceWordChk		: "Match whole word",

// Paste Operations / Dialog
PasteErrorCut	: "Your browser security settings don't permit the editor to automatically execute cutting operations. Please use the keyboard for that (Ctrl+X).",
PasteErrorCopy	: "Your browser security settings don't permit the editor to automatically execute copying operations. Please use the keyboard for that (Ctrl+C).",

PasteAsText		: "Paste as Plain Text",
PasteFromWord	: "Paste from Word",

DlgPasteMsg2	: "Please paste inside the following box using the keyboard (<strong>Ctrl+V</strong>) and hit <strong>OK</strong>.",
DlgPasteSec		: "Because of your browser security settings, the editor is not able to access your clipboard data directly. You are required to paste it again in this window.",
DlgPasteIgnoreFont		: "Ignore Font Face definitions",
DlgPasteRemoveStyles	: "Remove Styles definitions",
DlgPasteCleanBox		: "Clean Up Box",

// Color Picker
ColorAutomatic	: "Automatic",
ColorMoreColors	: "More Colors...",

// Document Properties
DocProps		: "Document Properties",

// Anchor Dialog
DlgAnchorTitle		: "Anchor Properties",
DlgAnchorName		: "Anchor Name",
DlgAnchorErrorName	: "Please type the anchor name",

// Speller Pages Dialog
DlgSpellNotInDic		: "Not in dictionary",
DlgSpellChangeTo		: "Change to",
DlgSpellBtnIgnore		: "Ignore",
DlgSpellBtnIgnoreAll	: "Ignore All",
DlgSpellBtnReplace		: "Replace",
DlgSpellBtnReplaceAll	: "Replace All",
DlgSpellBtnUndo			: "Undo",
DlgSpellNoSuggestions	: "- No suggestions -",
DlgSpellProgress		: "Spell check in progress...",
DlgSpellNoMispell		: "Spell check complete: No misspellings found",
DlgSpellNoChanges		: "Spell check complete: No words changed",
DlgSpellOneChange		: "Spell check complete: One word changed",
DlgSpellManyChanges		: "Spell check complete: %1 words changed",

IeSpellDownload			: "Spell checker not installed. Do you want to download it now?",

// Button Dialog
DlgButtonText		: "Text (Value)",
DlgButtonType		: "Type",
DlgButtonTypeBtn	: "Button",
DlgButtonTypeSbm	: "Submit",
DlgButtonTypeRst	: "Reset",

// Checkbox and Radio Button Dialogs
DlgCheckboxName		: "Name",
DlgCheckboxValue	: "Value",
DlgCheckboxSelected	: "Selected",

// Form Dialog
DlgFormName		: "Name",
DlgFormAction	: "Action",
DlgFormMethod	: "Method",

// Select Field Dialog
DlgSelectName		: "Name",
DlgSelectValue		: "Value",
DlgSelectSize		: "Size",
DlgSelectLines		: "lines",
DlgSelectChkMulti	: "Allow multiple selections",
DlgSelectOpAvail	: "Available Options",
DlgSelectOpText		: "Text",
DlgSelectOpValue	: "Value",
DlgSelectBtnAdd		: "Add",
DlgSelectBtnModify	: "Modify",
DlgSelectBtnUp		: "Up",
DlgSelectBtnDown	: "Down",
DlgSelectBtnSetValue : "Set as selected value",
DlgSelectBtnDelete	: "Delete",

// Textarea Dialog
DlgTextareaName	: "Name",
DlgTextareaCols	: "Columns",
DlgTextareaRows	: "Rows",

// Text Field Dialog
DlgTextName			: "Name",
DlgTextValue		: "Value",
DlgTextCharWidth	: "Character Width",
DlgTextMaxChars		: "Maximum Characters",
DlgTextType			: "Type",
DlgTextTypeText		: "Text",
DlgTextTypePass		: "Password",

// Hidden Field Dialog
DlgHiddenName	: "Name",
DlgHiddenValue	: "Value",

// Bulleted List Dialog
BulletedListProp	: "Bulleted List Properties",
NumberedListProp	: "Numbered List Properties",
DlgLstStart			: "Start",
DlgLstType			: "Type",
DlgLstTypeCircle	: "Circle",
DlgLstTypeDisc		: "Disc",
DlgLstTypeSquare	: "Square",
DlgLstTypeNumbers	: "Numbers (1, 2, 3)",
DlgLstTypeLCase		: "Lowercase Letters (a, b, c)",
DlgLstTypeUCase		: "Uppercase Letters (A, B, C)",
DlgLstTypeSRoman	: "Small Roman Numerals (i, ii, iii)",
DlgLstTypeLRoman	: "Large Roman Numerals (I, II, III)",

// Document Properties Dialog
DlgDocGeneralTab	: "General",
DlgDocBackTab		: "Background",
DlgDocColorsTab		: "Colors and Margins",
DlgDocMetaTab		: "Meta Data",

DlgDocPageTitle		: "Page Title",
DlgDocLangDir		: "Language Direction",
DlgDocLangDirLTR	: "Left to Right (LTR)",
DlgDocLangDirRTL	: "Right to Left (RTL)",
DlgDocLangCode		: "Language Code",
DlgDocCharSet		: "Character Set Encoding",
DlgDocCharSetCE		: "Central European",
DlgDocCharSetCT		: "Chinese Traditional (Big5)",
DlgDocCharSetCR		: "Cyrillic",
DlgDocCharSetGR		: "Greek",
DlgDocCharSetJP		: "Japanese",
DlgDocCharSetKR		: "Korean",
DlgDocCharSetTR		: "Turkish",
DlgDocCharSetUN		: "Unicode (UTF-8)",
DlgDocCharSetWE		: "Western European",
DlgDocCharSetOther	: "Other Character Set Encoding",

DlgDocDocType		: "Document Type Heading",
DlgDocDocTypeOther	: "Other Document Type Heading",
DlgDocIncXHTML		: "Include XHTML Declarations",
DlgDocBgColor		: "Background Color",
DlgDocBgImage		: "Background Image URL",
DlgDocBgNoScroll	: "Nonscrolling Background",
DlgDocCText			: "Text",
DlgDocCLink			: "Link",
DlgDocCVisited		: "Visited Link",
DlgDocCActive		: "Active Link",
DlgDocMargins		: "Page Margins",
DlgDocMaTop			: "Top",
DlgDocMaLeft		: "Left",
DlgDocMaRight		: "Right",
DlgDocMaBottom		: "Bottom",
DlgDocMeIndex		: "Document Indexing Keywords (comma separated)",
DlgDocMeDescr		: "Document Description",
DlgDocMeAuthor		: "Author",
DlgDocMeCopy		: "Copyright",
DlgDocPreview		: "Preview",

// Templates Dialog
Templates			: "Templates",
DlgTemplatesTitle	: "Content Templates",
DlgTemplatesSelMsg	: "Please select the template to open in the editor<br />(the actual contents will be lost):",
DlgTemplatesLoading	: "Loading templates list. Please wait...",
DlgTemplatesNoTpl	: "(No templates defined)",
DlgTemplatesReplace	: "Replace actual contents",

// About Dialog
DlgAboutAboutTab	: "About",
DlgAboutBrowserInfoTab	: "Browser Info",
DlgAboutLicenseTab	: "License",
DlgAboutVersion		: "version",
DlgAboutInfo		: "For further information go to"
};
