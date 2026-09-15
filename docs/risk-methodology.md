# LandSure Overall Risk Methodology

Version: v1.0
Status: Draft for review
Date: September 14, 2026
Description: This document defines the authoritative rule that the LandSure codebase must follow when deciding the overall risk status of a land parcel.

## 1. Purpose of this document

This is an internal engineering design document. It is not a user facing page by default. Its purpose is to define how the results of individual checks combine into a single overall status for a parcel.

This document is the authoritative rule that the code must implement. It is not a description of behavior that already exists somewhere else. If the combining logic in the codebase and this document ever disagree, the codebase is wrong and must be corrected to match this document.

Any future change to the combining logic must first be reflected here. A pull request that changes how the overall status is computed, without a corresponding update to this document, should not be merged.

## 2. First principle

The overall status reflects the strongest risk finding that LandSure can support with evidence. It is not an average of the individual checks. It is not a sum of the individual checks. It is not a percentage or a weighted score.

The overall status answers two questions at the same time: how serious is the worst thing LandSure found, and how complete was the screening that produced that answer. A parcel with one serious finding and several unknowns is not treated as "mostly fine." A parcel with no findings at all, but where several checks could not run, is not treated as "probably fine" either. Completeness of evidence is part of the answer, not a footnote to it.

## 3. The five possible overall statuses

LandSure defines exactly five overall statuses. No other value is valid as an overall status.

**HIGH RISK**
At least one connected risk check returned HIGH or VERY HIGH. LandSure has direct evidence of a serious problem.

**MODERATE RISK**
No connected risk check returned HIGH or VERY HIGH, but at least one connected risk check returned MODERATE. LandSure has evidence of a condition that deserves further investigation, without evidence of a severe one.

**LOW RISK**
Every connected risk check that LandSure can currently run has actually run, and every one of them returned LOW. LOW RISK is a strong claim: it means the full set of currently connected checks found nothing. It is never assigned when a check is missing, disconnected, or unknown, even if the checks that did run all came back LOW.

**UNKNOWN**
At least one relevant check could not produce a reliable result, and there is no HIGH or MODERATE finding to report instead. This is the default when LandSure's picture of the parcel is incomplete and no serious issue has already been found.

**NOT ASSESSED**
No checks have been run for this parcel at all. This is the starting state before any screening has taken place, and is distinct from a parcel that has been screened but where some checks returned UNKNOWN.

The distinction between LOW RISK and UNKNOWN is the most important rule in this document. LOW RISK requires that every meaningful check LandSure can currently run has actually run and returned LOW. Any missing check that could reveal a problem forces the overall to UNKNOWN instead of LOW. LandSure never upgrades an absence of information into a positive finding of safety.

## 4. What counts as a connected check

A connected check is a check that can produce a real result from real data, as opposed to a check that exists in the product concept but has no data source behind it yet.

Currently connected checks:

- Boundary quality
- Flood risk
- Waterway buffer

Currently not connected checks:

- Planning and zoning
- Land status
- Every other future check that has not yet been wired to a data source

A not connected check always contributes UNKNOWN. It never contributes HIGH, MODERATE, or LOW, because LandSure has no data source behind it yet and has nothing to evaluate.

## 5. Boundary is a data quality check, not a risk severity

Boundary measures whether the user submitted polygon is structurally valid: whether it is closed, whether it has a center point, and whether it can be processed by the rest of the pipeline. It does not measure the legal boundary, the registered boundary, or the Lands Commission boundary of the parcel.

Because of this, boundary does not participate directly in overall risk severity. A structurally valid polygon must never make the overall status LOW on its own. Boundary validity is a precondition for analysis, not a finding about the land.

However, if the boundary is unknown or invalid, no meaningful analysis of the parcel can be performed at all. In that case the overall status must be UNKNOWN, because none of the risk checks can be trusted to have evaluated the correct area.

## 6. The combining rule

The overall status is computed by checking the following conditions, in this exact order, and stopping at the first one that matches.

1. If any connected risk check returns HIGH or VERY HIGH, the overall is HIGH RISK.
2. Else if any connected risk check returns MODERATE, the overall is MODERATE RISK.
3. Else if any connected risk check returns UNKNOWN, the overall is UNKNOWN.
4. Else if every connected risk check returns LOW, the overall is LOW RISK.
5. Else the overall is UNKNOWN, as a safety net for any state not covered above.

### Pseudocode

```
function computeOverallStatus(connectedRiskChecks):
    anyHigh = false
    anyModerate = false
    anyUnknown = false
    allLow = true

    for check in connectedRiskChecks:
        if check.status == HIGH or check.status == VERY_HIGH:
            anyHigh = true
        if check.status == MODERATE:
            anyModerate = true
        if check.status == UNKNOWN:
            anyUnknown = true
        if check.status != LOW:
            allLow = false

    if anyHigh:
        return HIGH_RISK
    if anyModerate:
        return MODERATE_RISK
    if anyUnknown:
        return UNKNOWN
    if allLow:
        return LOW_RISK
    return UNKNOWN
```

### Example combinations

| Flood | Buffer | Planning | Land status | Overall |
|---|---|---|---|---|
| High | Low | Unknown | Unknown | HIGH RISK |
| Moderate | Low | Unknown | Unknown | MODERATE RISK |
| Low | Low | Unknown | Unknown | UNKNOWN |
| Low | Low | Low | Unknown | UNKNOWN |
| Low | Low | Low | Low | LOW RISK |
| Unknown | Low | Unknown | Unknown | UNKNOWN |
| Unknown | Unknown | Unknown | Unknown | UNKNOWN |

UNKNOWN outranks LOW, but it does not outrank HIGH or MODERATE. This ordering is intentional. If LandSure already has positive evidence of a serious issue, it reports that issue even if other checks are still incomplete, because a confirmed problem does not become less true just because something else is unknown. If LandSure has no positive evidence of a problem, but some checks are incomplete, it must not claim LOW, because LOW is a claim that the full available screening came back clean, and that claim would be false while a check is still missing.

## 7. No overall numeric score

LandSure does not produce an overall numeric score for a parcel. Combining several unrelated measurements, such as a flood model, a river buffer model, and a boundary validity check, into a single number would imply a level of precision that LandSure does not have and cannot defend. A single number invites a user to treat two parcels with different underlying evidence as directly comparable, when in fact their evidence bases may be completely different.

Individual check scores may continue to exist for internal ordering and for display on individual check cards. They must not be combined into a single overall score. The overall status defined in this document, one of the five values above, is the only overall level result LandSure produces.

## 8. Overall card messages

Each overall status has an exact user facing sentence. These sentences must be used as written wherever the overall status is shown to a user.

**HIGH RISK**
"One or more connected checks found a high risk condition. See the checks below for detail."

**MODERATE RISK**
"Some connected checks found conditions that warrant further investigation."

**LOW RISK**
"Every check LandSure can currently run returned a low risk result. This does not mean the land is safe. It means no known modeled risk was detected."

**UNKNOWN**
"One or more checks are still unknown, so LandSure cannot yet rule out a serious issue on this parcel."

**NOT ASSESSED**
"No checks have been run for this parcel yet."

## 9. Unknown versus not assessed

UNKNOWN and NOT ASSESSED are conceptually different, even though this document currently treats both as forcing the overall status to UNKNOWN.

UNKNOWN means the check exists in the product and was attempted for this parcel, but no reliable result could be produced. This can happen, for example, when a data source has a gap for a particular location.

NOT ASSESSED means the check exists as a concept in the product but has not yet been connected to any data source at all, for any parcel. Planning and zoning and land status are currently in this state.

Both currently force the overall to UNKNOWN, because in both cases LandSure lacks a reliable answer for that check. The recommendation is that the user interface distinguish the two cases when explaining why a parcel is UNKNOWN, so a user can tell the difference between "we tried and could not confirm" and "we do not check that yet." The database may continue to store both as the same underlying value until the corresponding checks are connected, since the distinction is a presentation concern rather than a change to the combining rule.

## 10. Adding a new check

A future check must be added to this methodology using the following steps.

- Classify the check as either a risk check or a data quality check.
- Define its LOW, MODERATE, HIGH, VERY HIGH, and UNKNOWN thresholds. A data quality check may use GOOD instead of LOW, since GOOD is a data quality label and not a risk severity.
- Declare whether the check is modeled, user provided, surveyor verified, or authoritative.
- Declare the check's known limitations, such as resolution, currency of the data, or geographic coverage.
- Add the check to the combining rule in Section 6 as a risk check, unless there is an explicit reason to classify it as a data quality check.

Data quality checks, such as boundary, must not affect the overall risk severity, except through the "no analysis possible" case described in Section 5, where an invalid or unknown boundary forces the overall to UNKNOWN.

## 11. What this means for the current codebase

The current codebase hard codes the overall status as UNKNOWN. Under this methodology, that hard coded value happens to be correct, because Planning and Land Status are not connected checks, and Section 5 requires the overall to be UNKNOWN whenever a relevant check has not run.

The recommendation is that the combining logic be extracted into a single, documented method that takes the individual check statuses as input and returns the overall status as output, following the rule in Section 6. This keeps the rule in one place in the code, so that any future connected check contributes to the overall status automatically, without a separate code change at every call site.

This extraction does not change any user facing result today. The overall status shown to users remains UNKNOWN until Planning and Land Status, or any other currently unconnected check, are connected to real data.

## 12. Open questions for the project owner

**Question 1: Should boundary contribute to overall severity?**
Recommended default: no. Boundary is data quality.

**Question 2: Should the priority order be HIGH, then MODERATE, then UNKNOWN, then LOW?**
Recommended default: yes.

**Question 3: Should the overall remain UNKNOWN as long as Planning or Land Status is not connected, even if every other connected check returns LOW?**
Recommended default: yes.

**Question 4: Should there be no overall numeric score?**
Recommended default: yes, no overall score.

**Question 5: Should the user interface distinguish UNKNOWN from NOT ASSESSED while the database stores both as the same value?**
Recommended default: yes.

**Question 6: Should the overall ever show LOW RISK while some checks remain permanently out of scope, with a visible caveat listing the missing checks?**
Recommended default: no, keep UNKNOWN with a clear note listing the missing checks.

## 13. Change log

| Version | Date | Description |
|---|---|---|
| v1.0 | September 14, 2026 | Initial draft of the LandSure Overall Risk Methodology. |